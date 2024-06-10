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
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);

        $validation = v::key('email', v::email()->noWhitespace())
            ->key('password', v::stringType())
            ->key('isRemember', v::boolVal());

        $validation->assert($data);
        $data = $this->auth->login($data);

        return $this->sendJson($data, StatusCode::STATUS_OK, 'User login successfully.');
    }
}
