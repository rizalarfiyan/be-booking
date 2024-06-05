<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\AbstractRepository;

class UserRepository extends AbstractRepository
{
    public function getAll()
    {
        return $this->db->query('SELECT * FROM users');
    }

    public function insert($payload)
    {
        return $this->db->insert('users', [
            'email' => $payload['email'],
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'password' => $payload['password'],
        ]);
    }
}
