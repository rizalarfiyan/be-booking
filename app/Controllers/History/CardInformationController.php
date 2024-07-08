<?php

declare(strict_types=1);

namespace App\Controllers\History;

use App\Services\AuthService;
use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CardInformationController extends BaseHistoryController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $userId = AuthService::getUserIdFromToken($req);
        $data = $this->history->getCardInformation($userId);

        return $this->sendJson($data, StatusCode::STATUS_OK, 'Get card information successfully.');
    }
}
