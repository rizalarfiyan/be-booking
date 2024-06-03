<?php
/**
 * @see       https://github.com/zendframework/zend-stratigility for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Booking\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class ErrorResponseGenerator
{
    /**
     * Create/update the response representing the error.
     */
    public function __invoke(
        Throwable $e,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface {
        $response = $response->withStatus(Utils::getStatusCode($e, $response));
        $body = $response->getBody();

        $body->write($response->getReasonPhrase() ?: 'Unknown Error');

        return $response;
    }
}
