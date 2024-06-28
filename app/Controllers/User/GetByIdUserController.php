<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Services\AuthService;
use Booking\Exception\BadRequestException;
use Booking\Exception\NotFoundException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class GetByIdUserController extends BaseUserController
{
    /**
     * @param int $id
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->user->getById($id);

        return $this->sendJson($data, StatusCode::STATUS_OK, 'User Successfully Requested.');
    }
}
