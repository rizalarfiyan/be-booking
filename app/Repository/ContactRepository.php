<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\BaseRepository;

class ContactRepository extends BaseRepository
{
    /**
     * Insert submitted contact form.
     *
     * @param $payload
     * @return int return the id of the inserted record
     */
    public function insert($payload): int
    {
        $this->db->insert('contacts', [
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'] ?? '',
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'message' => $payload['message'],
        ]);

        return $this->db->insertId();
    }
}
