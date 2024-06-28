<?php

declare(strict_types=1);

namespace App\Repository;

use App\Constants;
use Booking\Repository\BaseRepository;
use MeekroDBException;
use WhereClause;

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
     * @param $payload
     * @return WhereClause
     */
    protected function baseGetAll($payload): WhereClause
    {
        $where = new WhereClause('and');
        if (! empty($payload['search'])) {
            $where->add('name like %s', "%{$payload['search']}%");
        }

        return $where;
    }

    /*
     * Get all users.
     * @param $payload
     * @return mixed
     *
     */
    public function getAll($payload): mixed
    {
        $condition = $this->baseGetAll($payload);
        $orderBy = columnValidation([
            'user_id',
            'first_name',
            'last_name',
            'email',
            'status',
            'role',
            'points',
            'book_count',
            'created_at',
            'updated_at',
        ], $payload['orderType']) ?? 'created_at';
        $orderType = columnValidation(['ASC', 'DESC'], $payload['orderType']) ?? 'ASC';

        return $this->db->query('SELECT user_id, first_name, last_name, email, status, role, points, book_count, created_at, updated_at FROM users WHERE %l ORDER BY %l %l LIMIT %d OFFSET %d', $condition, $orderBy, $orderType, $payload['count'], $payload['page'] * $payload['count']);
    }

    /**
     * @param $payload
     * @return int
     */
    public function countAll($payload): int
    {
        $condition = $this->baseGetAll($payload);

        return (int) $this->db->queryFirstField('SELECT COUNT(user_id) FROM users WHERE %l', $condition) ?? 0;
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
     * Update user details.
     *
     * @param $payload
     * @return mixed
     * @throws MeekroDBException
     */
    public function updateUserDetails($payload): mixed
    {
        return $this->db->update('users', [
            'first_name' => $payload['firstName'],
            'last_name' => $payload['lastName'] ?? '',
            'email' => $payload['email'],
            'status' => $payload['status'],
            'role' => $payload['role'],
            'password' => $payload['password'],
        ], 'user_id=%d', $payload['userId']);
    }

    /**
     * Update user status.
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
     * Update user password.
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
        ) SELECT user_id, points, book_count, COALESCE((SELECT ranking FROM user_ranks WHERE user_id = %d), %s) AS ranking
        FROM users
        WHERE user_id = %d';

        return $this->db->queryFirstRow($query, Constants::LEADERBOARD_MAX_RANK, $userId, Constants::LEADERBOARD_MAX_RANK, $userId);
    }
}
