<?php

declare(strict_types=1);

namespace App\Controllers\User;

use Booking\Exception\BadRequestException;
use Booking\Exception\NotFoundException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class UpdateUserController extends BaseUserController
{
    /**
     * @param int $id
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     *
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);

        // Validation payload user_id, email, password (optional), first_name, last_name, status, role
        $validation = v::key('email', v::stringType()->email())
            ->key('first_name', v::stringType()->length(3, 50))
            ->key('last_name', v::stringType()->length(3, 50))
            ->key('status', v::stringType()->in(['active', 'inactive', 'banned']))
            ->key('role', v::stringType()->in(['admin', 'reader']));

        $validation->assert($data);
        $data['user_id'] = $id;

        try {
            $this->user->update($data);
        } catch (BadRequestException|UnprocessableEntitiesException|NotFoundException $e) {
            return $this->sendJson(null, StatusCode::STATUS_BAD_REQUEST, $e->getMessage());
        }

        return $this->sendJson($data, StatusCode::STATUS_OK, 'User Successfully Updated.');
    }
}
