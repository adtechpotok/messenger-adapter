<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enqueue\MessengerAdapter\Event;

use Interop\Queue\PsrMessage;
use Symfony\Component\EventDispatcher\Event;

class MessageDecodeFailEvent extends Event
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
     * @param PsrMessage $message
     * @param string     $queueName
     * @param \Throwable $e
     */
    public function __construct(PsrMessage $message, string $queueName, \Throwable $e)
    {
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
}
