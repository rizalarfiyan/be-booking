<?php

declare(strict_types=1);

namespace App\Repository;

use App\Constants;
use Booking\Repository\BaseRepository;
use MeekroDBException;

class UserRepository extends BaseRepository
{
    /**
     * Get user by id.
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        return $this->db->queryFirstRow('SELECT user_id, first_name, last_name, email, password, status, role, points, book_count FROM users where user_id = %d', $id);
    }

    /**
     * Get user by email.
     *
     * @param string $email
     * @return mixed
     */
    public function getByEmail(string $email): mixed
    {
        return $this->db->queryFirstRow('SELECT user_id, first_name, last_name, email, password, status, role, points, book_count FROM users where email = %s', $email);
    }

    /**
     * Insert user.
     *
     * @param $payload
     * @return int return the id of the inserted user
     */
    public function insert($payload): int
    {
        $this->db->insert('users', [
            'email' => $payload['email'],
            'first_name' => $payload['firstName'],
            'last_name' => $payload['lastName'] ?? '',
            'password' => $payload['password'],
        ]);

        return $this->db->insertId();
    }

    /**
     * Update user.
     *
     * @param string $status
     * @param int $userId
     * @return mixed
     * @throws MeekroDBException
     */
    public function updateStatus(string $status, int $userId): mixed
    {
        return $this->db->update('users', [
            'status' => $status,
        ], 'user_id=%d', $userId);
    }

    /**
     * @param string $password
     * @param int $userId
     * @return mixed
     * @throws MeekroDBException
     */
    public function updatePassword(string $password, int $userId): mixed
    {
        return $this->db->update('users', [
            'password' => $password,
        ], 'user_id=%d', $userId);
    }

    /**
     * Get top leaderboard.
     *
     * @return mixed
     */
    public function getTopLeaderboard() :mixed
    {
        return $this->db->query('SELECT user_id, first_name, last_name, email, points, book_count FROM users ORDER BY points DESC, created_at LIMIT %d', Constants::LEADERBOARD_LIMIT);
    }

    /**
     * get current rank of user.
     *
     * @param int $userId
     * @return mixed
     */
    public function getCurrentRank(int $userId):mixed
    {
        $query = 'WITH user_ranks AS (
            SELECT user_id, points, ROW_NUMBER() OVER (ORDER BY points DESC, created_at) AS ranking FROM users LIMIT %d
        ) SELECT points, book_count, COALESCE((SELECT ranking FROM user_ranks WHERE user_id = %d), %s) AS ranking
        FROM users
        WHERE user_id = %d';

        return $this->db->queryFirstRow($query, Constants::LEADERBOARD_MAX_RANK, $userId, Constants::LEADERBOARD_MAX_RANK.'+', $userId);
    }
}
