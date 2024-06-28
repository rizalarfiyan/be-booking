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
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);

        $validation = v::key('email', v::stringType()->email())
            ->key('password', v::stringType()->length(8, 50))
            ->key('first_name', v::stringType()->length(3, 50))
            ->key('last_name', v::stringType()->length(3, 50))
            ->key('status', v::stringType()->in(['active', 'inactive', 'banned']))
            ->key('role', v::stringType()->in(['admin', 'reader']));

        $validation->assert($data);
        try {
            $this->user->create($data);
        } catch (BadRequestException|UnprocessableEntitiesException $e) {
            return $this->sendJson(null, StatusCode::STATUS_BAD_REQUEST, $e->getMessage());
        }

        return $this->sendJson(null, StatusCode::STATUS_CREATED, 'User created successfully.');
    }
}
