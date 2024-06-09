<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class PostChangePassword extends BaseAuthController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);
        $code = $data['code'] ?? '';

        if (strlen($code) !== 50) {
            return $this->sendJson(null, StatusCode::STATUS_UNPROCESSABLE_ENTITY, 'Invalid change password code.');
        }

        $validation = v::key('password', v::stringType()->length(6, 36))
            ->keyValue('password_confirmation', 'equals', 'password');

        $validation->assert($data);
        $this->auth->changePassword($data);

        return $this->sendJson(null, StatusCode::STATUS_CREATED, 'Change password successfully.');
    }
}
