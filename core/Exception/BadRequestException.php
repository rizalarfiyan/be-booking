<?php

namespace Booking\Exception;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;

class BadRequestException extends BaseApiException
{
    /**
     * @param ?string $message
     * @param ?array $data
     * @param ?Exception $previous
     */
    public function __construct(string $message = null, $data = null, Exception $previous = null)
    {
        if (! $message) {
            $message = 'Invalid data provided.';
        }

        parent::__construct($message, StatusCode::STATUS_BAD_REQUEST, $previous, $data);
    }
}
