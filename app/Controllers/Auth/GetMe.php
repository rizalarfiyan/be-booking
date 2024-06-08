<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetMe extends BaseAuthController
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
        $id = $this->auth->getUserIdFromToken($req);
        $data = $this->auth->me($id);

        return $this->sendJson($data, StatusCode::STATUS_OK, 'Get user me successfully.');
    }
}
