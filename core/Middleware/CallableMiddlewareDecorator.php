<?php
/**
 * @see       https://github.com/zendframework/zend-stratigility for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Booking\Middleware;

use Booking\Exception\MissingResponseException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Decorate callable middleware as PSR-15 middleware.
 *
 * Decorates middleware with the following signature:
 *
 * <code>
 * function (
 *     ServerRequestInterface $request,
 *     RequestHandlerInterface $handler
 * ) : ResponseInterface
 * </code>
 *
 * such that it will operate as PSR-15 middleware.
 *
 * Neither the arguments nor the return value need be typehinted; however, if
 * the signature is incompatible, a PHP Error will likely be thrown.
 */
final class CallableMiddlewareDecorator implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $middleware;

    public function __construct(callable $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * @inheritDoc
     * @throws MissingResponseException if the decorated middleware
     *     fails to produce a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $response = ($this->middleware)($request, $handler);
        if (! $response instanceof ResponseInterface) {
            throw MissingResponseException::forCallableMiddleware($this->middleware);
        }

        return $response;
    }
}
