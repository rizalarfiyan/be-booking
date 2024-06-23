<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\UserRepository;
use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Repository\BaseRepository;
use MeekroDB;
use mysql_xdevapi\Collection;
use Throwable;

class LeaderboardService
{
    /** @var MeekroDB */
    protected MeekroDB $repo;

    /** @var UserRepository */
    protected UserRepository $user;

    /**
     * @param BaseRepository $repo
     */
    public function __construct(BaseRepository $repo)
    {
        $this->repo = $repo->db();
        $this->user = new UserRepository($this->repo);
    }

    /**
     * @param $leaderboard
     * @param bool $topRank
     * @return array
     */
    public static function response($leaderboard, bool $topRank = false): array
    {
        $data = [
            'userId' => (int) $leaderboard['user_id'],
            'points' => (int) $leaderboard['points'],
            'bookCount' => (int) $leaderboard['book_count'],
        ];

        if (! $topRank) {
            $data['ranking'] = (int) $leaderboard['ranking'];
        }

        if ($topRank) {
            $data['firstName'] = $leaderboard['first_name'];
            $data['lastName'] = $leaderboard['last_name'] ?? '';
            $data['email'] = $leaderboard['email'];
        }

        return $data;
    }

    /**
     * Get contact detail.
     *
     * @return array
     * @throws UnprocessableEntitiesException
     * @throws NotFoundException
     */
    public function getTopLeaderboard(): array
    {
        try {
            $data = $this->user->getTopLeaderboard();
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get top leaderboards.');
        }

        if (! $data) {
            throw new NotFoundException('Top leaderboards not found.');
        }

        return collect($data)->map(fn ($val) => self::response($val, true))->toArray();
    }

    /**
     * Get current rank.
     *
     * @param int $id
     * @return array
     * @throws NotFoundException
     * @throws UnprocessableEntitiesException
     */
    public function getCurrentRank(int $id): array
    {
        try {
            $data = $this->user->getCurrentRank($id);
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get current rank.');
        }

        if (! $data) {
            throw new NotFoundException('Current rank not found.');
        }

        return self::response($data);
    }
}
