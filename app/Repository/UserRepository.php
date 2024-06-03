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
}
