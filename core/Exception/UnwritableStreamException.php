<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Booking\Exception;

use RuntimeException;

class UnwritableStreamException extends RuntimeException implements ThrowableInterface
{
    public static function dueToConfiguration() : self
    {
        return new self('Stream is not writable');
    }

    public static function dueToMissingResource() : self
    {
        return new self('No resource available; cannot write');
    }

    public static function dueToPhpError() : self
    {
        return new self('Error writing to stream');
    }

    public static function forCallbackStream() : self
    {
        return new self('Callback streams cannot write');
    }
}
