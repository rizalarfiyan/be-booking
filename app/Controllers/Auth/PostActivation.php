<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostActivation extends BaseAuthController
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
            return $this->sendJson(null, StatusCode::STATUS_UNPROCESSABLE_ENTITY, 'Invalid activation code.');
        }

        $this->auth->activation($code);

        return $this->sendJson(null, StatusCode::STATUS_CREATED, 'Successfully activation.');
    }
}
