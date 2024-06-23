<?php

declare(strict_types=1);

namespace App\Controllers\Leaderboard;

use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;

class GetTopLeaderboardRankController extends BaseLeaderboardController
{
    /**
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(): ResponseInterface
    {
        $data = $this->leaderboard->getTopLeaderboard();

        return $this->sendJson($data, StatusCode::STATUS_OK, 'Get top leaderboard successfully.');
    }
}
