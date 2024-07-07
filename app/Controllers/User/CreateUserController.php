<?php

declare(strict_types=1);

namespace App\Controllers\User;

use Booking\Exception\BadRequestException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class CreateUserController extends BaseUserController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws BadRequestException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);

        $validation = v::key('email', v::stringType()->email())
            ->key('firstName', v::stringType()->length(3, 50))
            ->key('lastName', v::stringType()->length(3, 50))
            ->key('status', v::stringType()->in(['active', 'inactive', 'banned']))
            ->key('role', v::stringType()->in(['admin', 'reader']))
            ->key('password', v::stringType()->length(6, 36))
            ->keyValue('passwordConfirmation', 'equals', 'password');

        $validation->assert($data);
        $this->user->create($data);

        return $this->sendJson(null, StatusCode::STATUS_CREATED, 'User created successfully.');
    }
}
