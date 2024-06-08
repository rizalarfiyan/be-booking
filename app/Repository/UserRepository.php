<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\AbstractRepository;

class UserRepository extends AbstractRepository
{
    /**
     * Get all users.
     *
     * @return mixed
     */
    public function getAll(): mixed
    {
        return $this->db->query('SELECT * FROM users');
    }

    /**
     * Get user by id.
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        return $this->db->queryFirstRow('SELECT * FROM users where user_id = %s', $id);
    }

    /**
     * Get user by email.
     *
     * @param string $email
     * @return mixed
     */
    public function getByEmail(string $email): mixed
    {
        return $this->db->queryFirstRow('SELECT * FROM users where email = %s', $email);
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
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'password' => $payload['password'],
        ]);

        return $this->db->insertId();
    }

    /**
     * Insert user verifications.
     *
     * @param $payload
     * @return int return the id of the inserted verification
     */
    public function insertVerifications($payload): int
    {
        $this->db->insert('verifications', [
            'user_id' => $payload['user_id'],
            'code' => $payload['code'],
            'type' => $payload['type'],
        ]);

        return $this->db->insertId();
    }
}
