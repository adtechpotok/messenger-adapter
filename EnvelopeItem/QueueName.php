<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Enqueue\MessengerAdapter\EnvelopeItem;

use Symfony\Component\Messenger\EnvelopeItemInterface;

class QueueName implements EnvelopeItemInterface
{
    protected $queueName;

    /**
     * QueueName constructor.
     *
     * @param string $queueName
     */
    public function __construct(string $queueName = '')
    {
        $this->setQueueName($queueName);
    }

    /**
     * @return string
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }

    /**
     * @param string $queueName
     *
     * @return self
     */
    public function setQueueName(string $queueName)
    {
        $this->queueName = $queueName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->queueName);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->queueName = unserialize($serialized, array('allowed_classes' => false));
    }
}
