<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class PostForgotPassword extends BaseAuthController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);

        $validation = v::key('email', v::email()->noWhitespace());

        $validation->assert($data);
        $this->auth->forgotPassword($data['email']);

        return $this->sendJson(null, StatusCode::STATUS_OK, 'Forgot password send successfully.');
    }
}
