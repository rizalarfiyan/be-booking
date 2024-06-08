<?php

namespace Booking\Exception;

use Exception;
use Booking\Message\StatusCodeInterface as StatusCode;

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

        parent::__construct($message, StatusCode::STATUS_UNAUTHORIZED, $previous);
    }
}
