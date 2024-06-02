<?php
/**
 * @see       https://github.com/zendframework/zend-stratigility for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Booking\Middleware;

use Booking\Exception\MissingResponseException;
use Booking\Exception\MissingResponsePrototypeException;
use Booking\Response\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Decorate double-pass middleware as PSR-15 middleware.
 *
 * Decorates middleware with the following signature:
 *
 * <code>
 * function (
 *     ServerRequestInterface $request,
 *     ResponseInterface $response,
 *     callable $next
 * ) : ResponseInterface
 * </code>
 *
 * such that it will operate as PSR-15 middleware.
 *
 * Neither the arguments nor the return value need be typehinted; however, if
 * the signature is incompatible, a PHP Error will likely be thrown.
 */
final class DoublePassMiddlewareDecorator implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $middleware;

    /**
     * @var ResponseInterface
     */
    private ResponseInterface $responsePrototype;

    /**
     * @throws MissingResponsePrototypeException if no response
     *     prototype is present, and zend-diactoros is not installed.
     */
    public function __construct(callable $middleware, ResponseInterface $responsePrototype = null)
    {
        $this->middleware = $middleware;

        if (! $responsePrototype && ! class_exists(Response::class)) {
            throw MissingResponsePrototypeException::create();
        }

        $this->responsePrototype = $responsePrototype ?? new Response();
    }

    /**
     * {@inheritDoc}
     * @throws MissingResponseException if the decorated middleware
     *     fails to produce a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $response = ($this->middleware)(
            $request,
            $this->responsePrototype,
            $this->decorateHandler($handler)
        );

        if (! $response instanceof ResponseInterface) {
            throw MissingResponseException::forCallableMiddleware($this->middleware);
        }

        return $response;
    }

    private function decorateHandler(RequestHandlerInterface $handler) : callable
    {
        return function ($request, $response) use ($handler) {
            return $handler->handle($request);
        };
    }
}
