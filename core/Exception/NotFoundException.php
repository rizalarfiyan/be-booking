<?php

namespace Booking\Exception;

use Exception;
use Booking\Message\StatusCodeInterface as StatusCode;

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

        parent::__construct($message, StatusCode::STATUS_NOT_FOUND, $previous);
    }
}
