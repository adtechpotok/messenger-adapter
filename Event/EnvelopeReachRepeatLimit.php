<?php

namespace Enqueue\MessengerAdapter\Event;

use Interop\Queue\PsrMessage;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Messenger\Envelope;

class EnvelopeReachRepeatLimit extends Event
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
     * @var int
     */
    protected $attempt;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @param Envelope   $envelope
     * @param PsrMessage $message
     * @param string     $queueName
     * @param int        $attempt
     * @param int        $limit
     * @param \Throwable $e
     */
    public function __construct(Envelope $envelope, PsrMessage $message, string $queueName, int $attempt, int $limit, \Throwable $e)
    {
        $this->envelope = $envelope;
        $this->message = $message;
        $this->queueName = $queueName;
        $this->exception = $e;
        $this->attempt = $attempt;
        $this->limit = $limit;
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
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getAttempt(): int
    {
        return $this->attempt;
    }

    /**
     * @return Envelope
     */
    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }
}
