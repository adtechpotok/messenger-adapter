<?php

namespace Enqueue\MessengerAdapter\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class AbstractMessageFailLog
{
    use CreatedTrait;
    use UpdatedTrait;

    /**
     * @var int
     *
     * @ORM\Column(type="BigInt", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="exception_message", type="text", nullable=true)
     */
    protected $exceptionMessage;

    /**
     * @var string|null
     *
     * @ORM\Column(name="exception_class", type="text", nullable=true)
     */
    protected $exceptionClass;

    /**
     * @var string|null
     *
     * @ORM\Column(name="exception_trace", type="string", nullable=true)
     */
    protected $exceptionTrace;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message_body", type="text", nullable=true)
     */
    protected $messageBody;

    /**
     * @var array|null
     *
     * @ORM\Column(name="message_headers", type="json_array", nullable=true)
     */
    protected $messageHeaders;

    /**
     * @var array|null
     *
     * @ORM\Column(name="message_properties", type="json_array", nullable=true)
     */
    protected $messageProperties;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $envelope;

    /**
     * @var string|null
     *
     * @ORM\Column(name="queue_name", type="text", nullable=true)
     */
    protected $queueName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="event_type", type="text", nullable=true)
     */
    protected $eventType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="`limit`", type="text", nullable=true)
     */
    protected $limit;

    /**
     * @var int|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $attempt;

    public function __construct()
    {
        $this->createdNow();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return AbstractMessageFailLog
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExceptionMessage(): ?string
    {
        return $this->exceptionMessage;
    }

    /**
     * @param null|string $exceptionMessage
     *
     * @return AbstractMessageFailLog
     */
    public function setExceptionMessage(?string $exceptionMessage): self
    {
        $this->exceptionMessage = $exceptionMessage;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getExceptionClass(): ?string
    {
        return $this->exceptionClass;
    }

    /**
     * @param null|string $exceptionClass
     *
     * @return AbstractMessageFailLog
     */
    public function setExceptionClass(?string $exceptionClass): self
    {
        $this->exceptionClass = $exceptionClass;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExceptionTrace(): ?string
    {
        return $this->exceptionTrace;
    }

    /**
     * @param string|null $exceptionTrace
     *
     * @return AbstractMessageFailLog
     */
    public function setExceptionTrace(?string $exceptionTrace): self
    {
        $this->exceptionTrace = $exceptionTrace;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMessageBody(): ?string
    {
        return $this->messageBody;
    }

    /**
     * @param null|string $messageBody
     *
     * @return AbstractMessageFailLog
     */
    public function setMessageBody(?string $messageBody): self
    {
        $this->messageBody = $messageBody;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getMessageHeaders(): ?array
    {
        return $this->messageHeaders;
    }

    /**
     * @param array|null $messageHeaders
     *
     * @return AbstractMessageFailLog
     */
    public function setMessageHeaders(?array $messageHeaders): self
    {
        $this->messageHeaders = $messageHeaders;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getMessageProperties(): ?array
    {
        return $this->messageProperties;
    }

    /**
     * @param array|null $messageProperties
     *
     * @return AbstractMessageFailLog
     */
    public function setMessageProperties(?array $messageProperties): self
    {
        $this->messageProperties = $messageProperties;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEnvelope(): ?string
    {
        return $this->envelope;
    }

    /**
     * @param null|string $envelope
     *
     * @return AbstractMessageFailLog
     */
    public function setEnvelope(?string $envelope): self
    {
        $this->envelope = $envelope;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getQueueName(): ?string
    {
        return $this->queueName;
    }

    /**
     * @param null|string $queueName
     *
     * @return AbstractMessageFailLog
     */
    public function setQueueName(?string $queueName): self
    {
        $this->queueName = $queueName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     *
     * @return AbstractMessageFailLog
     */
    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAttempt(): ?int
    {
        return $this->attempt;
    }

    /**
     * @param int|null $attempt
     *
     * @return AbstractMessageFailLog
     */
    public function setAttempt(?int $attempt): self
    {
        $this->attempt = $attempt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    /**
     * @param null|string $eventType
     */
    public function setEventType(?string $eventType): void
    {
        $this->eventType = $eventType;
    }
}
