<?php

declare(strict_types=1);

namespace App\Controllers\Leaderboard;

use App\Controllers\Controller;
use App\Services\LeaderboardService;

class BaseLeaderboardController extends Controller
{
    /** @var LeaderboardService */
    protected LeaderboardService $leaderboard;

    /**
     * Inject the service in the base controller.
     *
     * @param LeaderboardService $leaderboard
     */
    public function __construct(LeaderboardService $leaderboard)
    {
        $this->leaderboard = $leaderboard;
    }
}
