<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Constants;
use App\Services\AuthService;
use Booking\Exception\ForbiddenException;
use Booking\Exception\UnauthorizedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Admin implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws UnauthorizedException
     * @throws ForbiddenException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $role = AuthService::getRoleFromToken($request);
        if ($role !== Constants::ROLE_ADMIN) {
            throw new ForbiddenException();
        }

        return $handler->handle($request);
    }
}
