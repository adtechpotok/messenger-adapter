<?php

namespace Enqueue\MessengerAdapter\Event;

use Interop\Queue\PsrMessage;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Messenger\Envelope;

class EnvelopeExecuteFailEvent extends Event
{
    /**
     * @var PsrMessage
     */
    protected $message;

    /**
     * @var string
     */
    protected $queueName;

    /**
     * @var \Throwable
     */
    protected $exception;

    /**
     * @var Envelope
     */
    protected $envelope;

    /**
     * @param Envelope   $envelope
     * @param PsrMessage $message
     * @param string     $queueName
     * @param \Throwable $e
     */
    public function __construct(Envelope $envelope, PsrMessage $message, string $queueName, \Throwable $e)
    {
        $this->envelope = $envelope;
        $this->message = $message;
        $this->queueName = $queueName;
        $this->exception = $e;
    }

    /**
     * @return PsrMessage
     */
    public function getMessage(): PsrMessage
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }

    /**
     * @return \Throwable
     */
    public function getException(): \Throwable
    {
        return $this->exception;
    }

    /**
     * @return Envelope
     */
    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }
}
