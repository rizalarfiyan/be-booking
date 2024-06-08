<?php

namespace Booking\Exception;

use Exception;

class UnauthorizedException extends BaseApiException
{
    /**
     * @param ?string    $message
     * @param ?Exception $previous
     */
    public function __construct(string $message = null, Exception $previous = null)
    {
        if (! $message) {
            $message = 'Unauthorized';
        }

        parent::__construct($message, 401, $previous);
    }
}
