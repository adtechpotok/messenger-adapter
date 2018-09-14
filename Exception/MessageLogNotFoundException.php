<?php

namespace Enqueue\MessengerAdapter\Exception;

use Symfony\Component\Messenger\Exception\ExceptionInterface;

class MessageLogNotFoundException extends \RuntimeException implements ExceptionInterface
{
}
