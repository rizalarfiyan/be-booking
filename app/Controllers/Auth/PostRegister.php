<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class PostRegister extends BaseAuthController
{
    /**
     * Get Home Page Api.
     *
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);

        $validation = v::key('email', v::email()->noWhitespace())
            ->key('first_name', v::alpha()->length(3, 50))
            ->key('last_name', v::optional(v::alpha()->length(3, 50)))
            ->key('password', v::stringType()->length(6, 36))
            ->keyValue('password_confirmation', 'equals', 'password');

        $validation->assert($data);
        $this->auth->register($data);

        return $this->sendJson(null, StatusCode::STATUS_CREATED, 'User registered successfully.');
    }
}
