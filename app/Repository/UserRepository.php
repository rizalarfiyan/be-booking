<?php

declare(strict_types=1);

namespace App\Repository;

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
        return $this->db->queryFirstRow('SELECT user_id, first_name, last_name, email, status, role, points, book_count FROM users where user_id = %d', $id);
    }

    /**
     * Get user by email.
     *
     * @param string $email
     * @return mixed
     */
    public function getByEmail(string $email): mixed
    {
        return $this->db->queryFirstRow('SELECT user_id, first_name, last_name, email, status, role, points, book_count FROM users where email = %s', $email);
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
}
