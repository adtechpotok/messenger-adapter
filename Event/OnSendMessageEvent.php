<?php

namespace Enqueue\MessengerAdapter\Event;

use Interop\Queue\PsrMessage;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Messenger\Envelope;

class OnSendMessageEvent extends Event
{
    /**
     * @var PsrMessage
     */
    protected $message;

    /**
     * @var Envelope
     */
    protected $envelope;

    /**
     * @param Envelope   $envelope
     * @param PsrMessage $message
     */
    public function __construct(Envelope $envelope, PsrMessage $message)
    {
        $this->envelope = $envelope;
        $this->message = $message;
    }

    /**
     * @return PsrMessage
     */
    public function getMessage(): PsrMessage
    {
        return $this->message;
    }

    /**
     * @param PsrMessage $message
     *
     * @return self
     */
    public function setMessage(PsrMessage $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Envelope
     */
    public function getEnvelope(): Envelope
    {
        return $this->envelope;
    }
}
