<?php

declare(strict_types=1);

namespace App\Controllers\User;

use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
