<?php

namespace Enqueue\MessengerAdapter\Classes\DTO;

use Interop\Queue\PsrMessage;
use Symfony\Component\Messenger\Envelope;

class MessageFailLogDTO
{
    /** @var PsrMessage */
    protected $message;

    /** @var string */
    protected $queueName;

    /** @var string */
    protected $eventType;

    /** @var \Throwable */
    protected $exception;

    /** @var Envelope|null */
    protected $envelope;

    /** @var int|null */
    protected $attempt;

    /** @var int|null */
    protected $limit;

    /**
     * @param PsrMessage    $message
     * @param string        $queueName
     * @param string        $eventType
     * @param \Throwable    $exception
     * @param null|Envelope $envelope
     * @param int|null      $attempt
     * @param int|null      $limit
     */
    public function __construct(
        PsrMessage $message,
        string $queueName,
        string $eventType,
        \Throwable $exception,
        ?Envelope $envelope = null,
        ?int $attempt = null,
        ?int $limit = null
    ) {
        $this->message = $message;
        $this->queueName = $queueName;
        $this->eventType = $eventType;
        $this->exception = $exception;
        $this->envelope = $envelope;
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
     * @return null|Envelope
     */
    public function getEnvelope(): ?Envelope
    {
        return $this->envelope;
    }

    /**
     * @return int|null
     */
    public function getAttempt(): ?int
    {
        return $this->attempt;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return string|null
     */
    public function getEventType(): ?string
    {
        return $this->eventType;
    }
}
