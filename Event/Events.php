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

abstract class Events
{
    public const MESSAGE_DECODE_FAIL = 'MESSAGE_DECODE_FAIL';
    public const ENVELOPE_EXECUTE_FAIL = 'ENVELOPE_EXECUTE_FAIL';
    public const ENVELOPE_REACH_REPEAT_LIMIT = 'ENVELOPE_REACH_REPEAT_LIMIT';
    public const ENVELOPE_FAIL_ON_REPEAT = 'ENVELOPE_FAIL_ON_REPEAT';
}
