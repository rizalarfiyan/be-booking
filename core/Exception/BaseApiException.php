<?php

declare(strict_types=1);

namespace Booking\Exception;

use Exception;

class BaseApiException extends Exception
{
    /**
     * @var ?array
     */
    public ?array $data;

    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     * @param array|null $data
     */
    public function __construct(string $message, int $code, Exception $previous = null, ?array $data = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }
}
