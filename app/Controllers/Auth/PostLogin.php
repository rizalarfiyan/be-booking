<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class PostLogin extends BaseAuthController
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
            ->key('password', v::stringType());

        $validation->assert($data);
        $this->auth->login($data);

        return $this->sendJson(null, StatusCode::STATUS_OK, 'User login successfully.');
    }
}
