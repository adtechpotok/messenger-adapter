<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enqueue\MessengerAdapter;

use Adtechpotok\Bundle\EnqueueMessengerAdapterBundle\EnvelopeItem\RoutingKeyItem;
use Enqueue\AmqpTools\DelayStrategy;
use Enqueue\AmqpTools\DelayStrategyAware;
use Enqueue\AmqpTools\RabbitMqDelayPluginDelayStrategy;
use Enqueue\MessengerAdapter\EnvelopeItem\QueueName;
use Enqueue\MessengerAdapter\EnvelopeItem\RepeatMessage;
use Enqueue\MessengerAdapter\Event\EnvelopeExecuteFailEvent;
use Enqueue\MessengerAdapter\Event\EnvelopeFailOnRepeat;
use Enqueue\MessengerAdapter\Event\EnvelopeReachRepeatLimit;
use Enqueue\MessengerAdapter\Event\MessageDecodeFailEvent;
use Enqueue\MessengerAdapter\Event\Events;
use Enqueue\MessengerAdapter\Exception\RepeatMessageException;
use Enqueue\MessengerAdapter\Exception\RequeueMessageException;
use Interop\Amqp\AmqpMessage;
use Interop\Amqp\AmqpTopic;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\DecoderInterface;
use Symfony\Component\Messenger\Transport\Serialization\EncoderInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Interop\Queue\Exception as InteropQueueException;
use Enqueue\MessengerAdapter\Exception\SendingMessageFailedException;
use Enqueue\MessengerAdapter\EnvelopeItem\TransportConfiguration;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Symfony Messenger transport.
 *
 * @author Samuel Roze <samuel.roze@gmail.com>
 * @author Max Kotliar <kotlyar.maksim@gmail.com>
 */
class QueueInteropTransport implements TransportInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var ContextManager
     */
    private $contextManager;

    /**
     * @var array
     */
    private $options;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var
     */
    private $shouldStop;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param DecoderInterface         $decoder
     * @param EncoderInterface         $encoder
     * @param ContextManager           $contextManager
     * @param array                    $options
     * @param bool                     $debug
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        DecoderInterface $decoder,
        EncoderInterface $encoder,
        ContextManager $contextManager,
        array $options = array(),
        bool $debug = false
    ) {
        $this->dispatcher = $dispatcher;

        $this->decoder = $decoder;
        $this->encoder = $encoder;
        $this->contextManager = $contextManager;
        $this->debug = $debug;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function receive(callable $handler): void
    {
        $psrContext = $this->contextManager->psrContext();
        $destination = $this->getDestination(null);

        foreach ($destination['queue'] as $name) {
            $queue = $psrContext->createQueue($name);
            $consumer = $psrContext->createConsumer($queue);

            if ($this->debug) {
                $this->contextManager->ensureExists($destination);
            }

            while (!$this->shouldStop) {
                try {
                    if (null === ($message = $consumer->receive($this->options['receiveTimeout'] ?? 0))) {
                        $handler(null);
                        continue;
                    }
                } catch (\Exception $e) {
                    if ($this->contextManager->recoverException($e, $destination)) {
                        continue;
                    }

                    throw $e;
                }

                try {
                    $envelope = $this->decoder->decode(array(
                        'body' => $message->getBody(),
                        'headers' => $message->getHeaders(),
                        'properties' => $message->getProperties(),
                    ))
                        ->with(new QueueName($name));
                } catch (\Throwable $e) {
                    $consumer->reject($message);

                    $this->dispatcher->dispatch(
                        Events::MESSAGE_DECODE_FAIL,
                        new MessageDecodeFailEvent($message, $name, $e)
                    );

                    continue;
                }

                try {
                    $handler($envelope);

                    $consumer->acknowledge($message);
                } catch (RepeatMessageException $e) {
                    $consumer->reject($message);

                    $repeat = $envelope->get(RepeatMessage::class);

                    if (null === $repeat) {
                        $repeat = new RepeatMessage($e->getTimeToDelay(), $e->getMaxAttempts());
                    }

                    if ($repeat->isRepeatable()) {
                        try {
                            $this->send($envelope->with($repeat));
                        } catch (\Throwable $e) {
                            $this->dispatcher->dispatch(
                                Events::ENVELOPE_FAIL_ON_REPEAT,
                                new EnvelopeFailOnRepeat($envelope, $message, $name, $repeat->getAttempts(),
                                    $repeat->getMaxAttempts(), $e)
                            );
                        }
                    } else {
                        $this->dispatcher->dispatch(
                            Events::ENVELOPE_REACH_REPEAT_LIMIT,
                            new EnvelopeReachRepeatLimit($envelope, $message, $name, $repeat->getAttempts(),
                                $repeat->getMaxAttempts(), $e)
                        );
                    }
                } catch (RequeueMessageException $e) {
                    $consumer->reject($message, true);
                } catch (\Throwable $e) {
                    $consumer->reject($message);

                    $this->dispatcher->dispatch(
                        Events::ENVELOPE_EXECUTE_FAIL,
                        new EnvelopeExecuteFailEvent($envelope, $message, $name, $e)
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send(Envelope $message): void
    {
        $psrContext = $this->contextManager->psrContext();
        $destination = $this->getDestination($message);
        $topic = $psrContext->createTopic($destination['topic']['name']);

        if ($this->debug) {
            $this->contextManager->ensureExists($destination);
        }

        $encodedMessage = $this->encoder->encode($message);

        /** @var AmqpMessage $psrMessage */
        $psrMessage = $psrContext->createMessage(
            $encodedMessage['body'],
            $encodedMessage['properties'] ?? array(),
            $encodedMessage['headers'] ?? array()
        );

        /** @var RoutingKeyItem $routingKeyItem */
        if (null !== $routingKeyItem = $message->get(RoutingKeyItem::class)) {
            $psrMessage->setRoutingKey($routingKeyItem->getRoutingKey());
        }

        $producer = $psrContext->createProducer();

        if (isset($this->options['deliveryDelay'])) {
            if ($producer instanceof DelayStrategyAware) {
                $producer->setDelayStrategy($this->options['delayStrategy']);
            }
            $producer->setDeliveryDelay($this->options['deliveryDelay']);
        }
        if (isset($this->options['priority'])) {
            $producer->setPriority($this->options['priority']);
        }
        if (isset($this->options['timeToLive'])) {
            $producer->setTimeToLive($this->options['timeToLive']);
        }

        /** @var RepeatMessage $repeat */
        $repeat = $message->get(RepeatMessage::class);
        if (null !== $repeat) {
            if ($producer instanceof DelayStrategyAware) {
                $producer->setDelayStrategy($this->options['delayStrategy']);
            }
            $producer->setDeliveryDelay($repeat->getNowDelayToMs());
        }

        try {
            $producer->send($topic, $psrMessage);
        } catch (InteropQueueException $e) {
            if ($this->contextManager->recoverException($e, $destination)) {
                // The context manager recovered the exception, we re-try.
                $this->send($message);

                return;
            }

            throw new SendingMessageFailedException($e->getMessage(), null, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stop(): void
    {
        $this->shouldStop = true;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(array(
            'receiveTimeout' => null,
            'deliveryDelay' => null,
            'delayStrategy' => RabbitMqDelayPluginDelayStrategy::class,
            'priority' => null,
            'timeToLive' => null,
            'topic' => array('name' => 'messages', 'type' => AmqpTopic::TYPE_TOPIC),
            'queue' => array(array('name' => 'messages')),
        ));

        $resolver->setAllowedTypes('receiveTimeout', array('null', 'int'));
        $resolver->setAllowedTypes('deliveryDelay', array('null', 'int'));
        $resolver->setAllowedTypes('priority', array('null', 'int'));
        $resolver->setAllowedTypes('timeToLive', array('null', 'int'));
        $resolver->setAllowedTypes('delayStrategy', array('null', 'string'));

        $resolver->setNormalizer('delayStrategy', function (Options $options, $value) {
            if (null === $value) {
                return null;
            }

            $delayStrategy = new $value();
            if (!$delayStrategy instanceof DelayStrategy) {
                throw new \InvalidArgumentException(sprintf(
                    'The delayStrategy option must be either instance of "%s", but got "%s"',
                    DelayStrategy::class,
                    $value
                ));
            }

            return $delayStrategy;
        });
    }

    public function getDestination(?Envelope $message): array
    {
        /** @var TransportConfiguration|null $configuration */
        $configuration = $message ? $message->get(TransportConfiguration::class) : null;
        $topic = null !== $configuration ? $configuration->getTopic() : null;
        $result = array(
            'topic' => array(
                'name' => $topic ?? $this->options['topic']['name'],
                'type' => $this->options['topic']['type'] ?? AmqpTopic::TYPE_TOPIC,
            ),
        );

        foreach ($this->options['queue'] as $routingKey => $queue) {
            $result['queue'][$routingKey] = $queue['name'];
        }

        return $result;
    }
}
