<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use Booking\Constants;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use Exception;
use Booking\Message\StatusCodeInterface as StatusCode;

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

        try {
            $this->auth->register($data);
        } catch (Exception $e) {
            if ($e->getCode() === 1062) {
                return $this->sendJson([
                    'email' => 'Email already exists.'
                ], StatusCode::STATUS_UNPROCESSABLE_ENTITY, Constants::VALIDATION_MESSAGE);
            }
            throw $e;
        }

        return $this->sendJson(null, StatusCode::STATUS_CREATED, "User registered successfully.");
    }
}
