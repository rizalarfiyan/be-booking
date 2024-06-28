<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Services\AuthService;
use Booking\Exception\BadRequestException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class GetAllUserController extends BaseUserController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $metadata = $this->getDatatable($req);
        $users = $this->user->getAll($metadata);

        return $this->sendJson($this->listResponse($users, $metadata), StatusCode::STATUS_OK, 'Get all user successfully.');
    }
}
