<?php
/**
 * @see       https://github.com/zendframework/zend-stratigility for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Booking\Exception;

use UnexpectedValueException;
use Booking\Middleware\DoublePassMiddlewareDecorator;

/**
 * Exception thrown by the DoublePassMiddlewareDecorator when no response
 * prototype is provided, and Diactoros is not available to create a default.
 */
class MissingResponsePrototypeException extends UnexpectedValueException implements ExceptionInterface
{
    public static function create(): self
    {
        return new self(sprintf(
            'Unable to create a %s instance; no response prototype provided,'
            . ' and zendframework/zend-diactoros is not installed',
            DoublePassMiddlewareDecorator::class
        ));
    }
}
