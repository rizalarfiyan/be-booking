<?php

declare(strict_types=1);

namespace App\Controllers\User;

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
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);

        $validation = v::key('status', v::stringType()->in(['active', 'inactive', 'banned']))
            ->key('role', v::stringType()->in(['admin', 'reader']));

        $validation->assert($data);
        $data['userId'] = $id;
        $this->user->update($data);

        return $this->sendJson(null, StatusCode::STATUS_OK, 'User Successfully Updated.');
    }
}
