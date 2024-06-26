<?php

declare(strict_types=1);

namespace App\Controllers\Leaderboard;

use App\Controllers\Contact\BaseContactController;
use App\Services\AuthService;
use Booking\Exception\NotFoundException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetCurrentRankController extends BaseLeaderboardController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws UnprocessableEntitiesException
     * @throws UnauthorizedException
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $id = AuthService::getUserIdFromToken($req);
        $contact = $this->leaderboard->getCurrentRank($id);

        return $this->sendJson($contact, StatusCode::STATUS_OK, 'Get current rank successfully.');
    }
}
