<?php

namespace Enqueue\MessengerAdapter\EventSubscriber;

use Enqueue\MessengerAdapter\Classes\DTO\MessageFailLogDTO;
use Enqueue\MessengerAdapter\Event\EnvelopeExecuteFailEvent;
use Enqueue\MessengerAdapter\Event\EnvelopeFailOnRepeat;
use Enqueue\MessengerAdapter\Event\EnvelopeReachRepeatLimit;
use Enqueue\MessengerAdapter\Event\Events;
use Enqueue\MessengerAdapter\Event\MessageDecodeFailEvent;
use Enqueue\MessengerAdapter\Service\Contract\MessageFailLogStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class QueueInteropTransportFailSubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageFailLogStorageInterface
     */
    protected $storage;

    /**
     * QueueInteropTransportFailSubscriber constructor.
     *
     * @param MessageFailLogStorageInterface $storage
     */
    public function __construct(MessageFailLogStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            Events::ENVELOPE_FAIL_ON_REPEAT => array(
                array('onEnvelopeFailOnRepeat', 0),
            ),
            Events::ENVELOPE_EXECUTE_FAIL => array(
                array('onEnvelopeExecuteFail', 0),
            ),
            Events::ENVELOPE_REACH_REPEAT_LIMIT => array(
                array('onEnvelopeReachRepeatLimit', 0),
            ),
            Events::MESSAGE_DECODE_FAIL => array(
                array('onMessageDecodeFail', 0),
            ),
        );
    }

    /**
     * @param EnvelopeFailOnRepeat $event
     */
    public function onEnvelopeFailOnRepeat(EnvelopeFailOnRepeat $event): void
    {
        $this->storage->log(new MessageFailLogDTO(
            $event->getMessage(),
            $event->getQueueName(),
            'onEnvelopeFailOnRepeat',
            $event->getException(),
            $event->getEnvelope(),
            $event->getAttempt(),
            $event->getLimit()
        ));
    }

    /**
     * @param EnvelopeExecuteFailEvent $event
     */
    public function onEnvelopeExecuteFail(EnvelopeExecuteFailEvent $event): void
    {
        $this->storage->log(new MessageFailLogDTO(
            $event->getMessage(),
            $event->getQueueName(),
            'onEnvelopeFailOnRepeat',
            $event->getException(),
            $event->getEnvelope()
        ));
    }

    /**
     * @param EnvelopeReachRepeatLimit $event
     */
    public function onEnvelopeReachRepeatLimit(EnvelopeReachRepeatLimit $event): void
    {
        $this->storage->log(new MessageFailLogDTO(
            $event->getMessage(),
            $event->getQueueName(),
            'onEnvelopeReachRepeatLimit',
            $event->getException(),
            $event->getEnvelope(),
            $event->getAttempt(),
            $event->getLimit()
        ));
    }

    /**
     * @param MessageDecodeFailEvent $event
     */
    public function onMessageDecodeFail(MessageDecodeFailEvent $event): void
    {
        $this->storage->log(new MessageFailLogDTO(
            $event->getMessage(),
            $event->getQueueName(),
            'onMessageDecodeFail',
            $event->getException()
        ));
    }
}
