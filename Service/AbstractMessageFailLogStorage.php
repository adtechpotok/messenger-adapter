<?php

namespace Enqueue\MessengerAdapter\Service;

use Doctrine\ORM\EntityManagerInterface;
use Enqueue\MessengerAdapter\Classes\DTO\MessageFailLogDTO;
use Enqueue\MessengerAdapter\Entity\AbstractMessageFailLog;
use Enqueue\MessengerAdapter\Exception\MessageLogNotFoundException;
use Enqueue\MessengerAdapter\Service\Contract\MessageFailLogStorageInterface;

abstract class AbstractMessageFailLogStorage implements MessageFailLogStorageInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function log(MessageFailLogDTO $dto): void
    {
        $entity = $this->getEntity();

        $entity->setQueueName($dto->getQueueName())
            ->setAttempt($dto->getAttempt())
            ->setLimit($dto->getLimit())
            ->setEventType($dto->getEventType());

        $message = $dto->getMessage();
        if ($message) {
            $entity->setMessageBody($message->getBody())
                ->setMessageHeaders($message->getHeaders())
                ->setMessageProperties($message->getProperties());
        }

        $envelope = $dto->getEnvelope();
        if ($envelope) {
            $entity->setEnvelope(\serialize($envelope));
        }

        $exception = $dto->getException();
        if ($exception) {
            $entity
                ->setExceptionClass(\get_class($exception))
                ->setExceptionMessage($exception->getMessage())
                ->setExceptionTrace($exception->getTraceAsString());
        }

        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @throws MessageLogNotFoundException
     */
    public function getByIdentifier($identifier): AbstractMessageFailLog
    {
        /** @var AbstractMessageFailLog $entity */
        $entity = $this->em->getRepository(\get_class($this->getEntity()))->find($identifier);

        if (!$entity) {
            throw new MessageLogNotFoundException(sprintf('Identifier %s is not present in database', $identifier));
        }

        return $entity;
    }
}
