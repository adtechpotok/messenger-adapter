<?php

namespace Enqueue\MessengerAdapter\Service\Contract;

use Enqueue\MessengerAdapter\Classes\DTO\MessageFailLogDTO;
use Enqueue\MessengerAdapter\Entity\AbstractMessageFailLog;

interface MessageFailLogStorageInterface
{
    /**
     * @param MessageFailLogDTO $dto
     *
     * @return mixed
     */
    public function log(MessageFailLogDTO $dto);

    /**
     * @param $identifier
     *
     * @return AbstractMessageFailLog
     */
    public function getByIdentifier($identifier): AbstractMessageFailLog;

    /**
     * @return AbstractMessageFailLog
     */
    public function getEntity(): AbstractMessageFailLog;
}
