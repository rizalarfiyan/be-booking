<?php

namespace Booking\Exception;

use Exception;

class NotFoundException extends BaseApiException
{
    /**
     * @param ?string $message
     * @param ?Exception $previous
     */
    public function __construct(string $message = null, Exception $previous = null)
    {
        if (! $message) {
            $message = 'Resource not found';
        }

        parent::__construct($message, 404, $previous);
    }
}
