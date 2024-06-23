<?php

namespace Booking\Exception;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;

class ForbiddenException extends BaseApiException
{
    /**
     * @param ?string    $message
     * @param ?Exception $previous
     */
    public function __construct(string $message = null, Exception $previous = null)
    {
        if (! $message) {
            $message = 'Forbidden. You do not have permission to access this resource.';
        }

        parent::__construct($message, StatusCode::STATUS_FORBIDDEN, $previous);
    }
}
