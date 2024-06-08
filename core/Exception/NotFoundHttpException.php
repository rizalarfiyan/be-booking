<?php

namespace Booking\Exception;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Booking\Message\StatusCodeInterface as StatusCode;

class NotFoundHttpException extends BaseApiException
{
    /**
     * @param ServerRequestInterface $request
     * @param ?Exception $previous
     */
    public function __construct(ServerRequestInterface $request, Exception $previous = null)
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        $message = "Method {$method} was not found in path {$path}";

        parent::__construct($message, StatusCode::STATUS_NOT_FOUND, $previous);
    }
}
