<?php
/**
 * @see       https://github.com/zendframework/zend-stratigility for the canonical source repository
 * @copyright Copyright (c) 2016-2019 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-stratigility/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Booking\Middleware;

use ErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * Error handler middleware.
 *
 * Use this middleware as the outermost (or close to outermost) middleware
 * layer, and use it to intercept PHP errors and exceptions.
 *
 * The class offers two extension points:
 *
 * - Error response generators.
 * - Listeners.
 *
 * Error response generators are callables with the following signature:
 *
 * <code>
 * function (
 *     Throwable $e,
 *     ServerRequestInterface $request,
 *     ResponseInterface $response
 * ) : ResponseInterface
 * </code>
 *
 * These are provided the error, and the request responsible; the response
 * provided is the response prototype provided to the ErrorHandler instance
 * itself, and can be used as the basis for returning an error response.
 *
 * An error response generator must be provided as a constructor argument;
 * if not provided, an instance of Zend\Stratigility\Middleware\ErrorResponseGenerator
 * will be used.
 *
 * Listeners use the following signature:
 *
 * <code>
 * function (
 *     Throwable $e,
 *     ServerRequestInterface $request,
 *     ResponseInterface $response
 * ) : void
 * </code>
 *
 * Listeners are given the error, the request responsible, and the generated
 * error response, and can then react to them. They are best suited for
 * logging and monitoring purposes.
 *
 * Listeners are attached using the attachListener() method, and triggered
 * in the order attached.
 */
class ErrorHandler implements MiddlewareInterface
{
    /**
     * @var callable[]
     */
    private array $listeners = [];

    /**
     * @var callable Routine that will generate the error response.
     */
    private $responseGenerator;

    /**
     * @var callable
     */
    private $responseFactory;

    /**
     * @param callable $responseFactory A factory capable of returning an
     *     empty ResponseInterface instance to update and return when returning
     *     an error response.
     * @param null|callable $responseGenerator Callback that will generate the final
     *     error response; if none is provided, ErrorResponseGenerator is used.
     */
    public function __construct(callable $responseFactory, callable $responseGenerator = null)
    {
        $this->responseFactory = function () use ($responseFactory) : ResponseInterface {
            return $responseFactory();
        };
        $this->responseGenerator = $responseGenerator ?: new ErrorResponseGenerator();
    }

    /**
     * Attach an error listener.
     *
     * Each listener receives the following three arguments:
     *
     * - Throwable $error
     * - ServerRequestInterface $request
     * - ResponseInterface $response
     *
     * These instances are all immutable, and the return values of
     * listeners are ignored; use listeners for reporting purposes
     * only.
     */
    public function attachListener(callable $listener) : void
    {
        if (in_array($listener, $this->listeners, true)) {
            return;
        }

        $this->listeners[] = $listener;
    }

    /**
     * Middleware to handle errors and exceptions in layers it wraps.
     *
     * Adds an error handler that will convert PHP errors to ErrorException
     * instances.
     *
     * Internally, wraps the call to $next() in a try/catch block, catching
     * all PHP Throwable.
     *
     * When an exception is caught, an appropriate error response is created
     * and returned instead; otherwise, the response returned by $next is
     * used.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        set_error_handler($this->createErrorHandler());

        try {
            $response = $handler->handle($request);
        } catch (Throwable $e) {
            $response = $this->handleThrowable($e, $request);
        }

        restore_error_handler();

        return $response;
    }

    /**
     * Handles all throwable, generating and returning a response.
     *
     * Passes the error, request, and response prototype to createErrorResponse(),
     * triggers all listeners with the same arguments (but using the response
     * returned from createErrorResponse()), and then returns the response.
     */
    private function handleThrowable(Throwable $e, ServerRequestInterface $request) : ResponseInterface
    {
        $generator = $this->responseGenerator;
        $response = $generator($e, $request, ($this->responseFactory)());
        $this->triggerListeners($e, $request, $response);

        return $response;
    }

    /**
     * Creates and returns a callable error handler that raises exceptions.
     *
     * Only raises exceptions for errors that are within the error_reporting mask.
     */
    private function createErrorHandler() : callable
    {
        /*
         * @throws ErrorException if error is not within the error_reporting mask.
         */
        return function (int $errno, string $errStr, string $errFile, int $errLine) : void {
            if (! (error_reporting() & $errno)) {
                // error_reporting does not include this error
                return;
            }

            throw new ErrorException($errStr, 0, $errno, $errFile, $errLine);
        };
    }

    /**
     * Trigger all error listeners.
     */
    private function triggerListeners(
        Throwable $error,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : void {
        foreach ($this->listeners as $listener) {
            $listener($error, $request, $response);
        }
    }
}
