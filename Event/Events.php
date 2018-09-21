<?php

namespace Enqueue\MessengerAdapter\Event;

abstract class Events
{
    public const MESSAGE_DECODE_FAIL = 'MESSAGE_DECODE_FAIL';
    public const ENVELOPE_EXECUTE_FAIL = 'ENVELOPE_EXECUTE_FAIL';
    public const ENVELOPE_REACH_REPEAT_LIMIT = 'ENVELOPE_REACH_REPEAT_LIMIT';
    public const ENVELOPE_FAIL_ON_REPEAT = 'ENVELOPE_FAIL_ON_REPEAT';
    public const ON_SEND_MESSAGE = 'ON_SEND_MESSAGE';
}
