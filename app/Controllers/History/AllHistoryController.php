<?php

declare(strict_types=1);

namespace App\Controllers\History;

use App\Constants;
use App\Services\AuthService;
use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AllHistoryController extends BaseHistoryController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $userId = AuthService::getUserIdFromToken($req);
        $role = AuthService::getRoleFromToken($req);
        $metadata = $this->getDatatable($req);
        $metadata['userId'] = $userId;
        $metadata['isAdmin'] = $role === Constants::ROLE_ADMIN;
        $data = $this->history->getAll($metadata);

        return $this->sendJson($this->listResponse($data, $metadata), StatusCode::STATUS_OK, 'Get all history successfully.');
    }
}
