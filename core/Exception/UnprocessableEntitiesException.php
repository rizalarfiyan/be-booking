<?php

namespace Booking\Exception;

use Exception;
use Booking\Message\StatusCodeInterface as StatusCode;

class UnprocessableEntitiesException extends BaseApiException
{
    /**
     * @param ?string $message
     * @param ?array $data
     * @param ?Exception $previous
     */
    public function __construct(string $message = null, $data = null, Exception $previous = null)
    {
        if (! $message) {
            $message = 'Could not process the request, please try again later.';
        }

        parent::__construct($message, StatusCode::STATUS_UNPROCESSABLE_ENTITY, $previous, $data);
    }
}
