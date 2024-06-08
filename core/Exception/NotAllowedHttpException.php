<?php

namespace Booking\Exception;

use Exception;
use Psr\Http\Message\ServerRequestInterface;

class NotAllowedHttpException extends BaseApiException
{
    /**
     * @param ServerRequestInterface $request
     * @param ?Exception $previous
     */
    public function __construct(ServerRequestInterface $request, Exception $previous = null)
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        $message = "Method {$method} is not allowed in path {$path}";

        parent::__construct($message, 405, $previous);
    }
}
