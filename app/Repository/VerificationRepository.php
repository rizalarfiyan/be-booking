<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\BaseRepository;
use MeekroDBException;

class VerificationRepository extends BaseRepository
{
    /**
     * Get verification by code.
     *
     * @param string $code
     * @return mixed
     */
    public function getByCode(string $code): mixed
    {
        return $this->db->queryFirstRow('SELECT verification_id, user_id, code, type, created_at, expired_at FROM verifications where code = %s', $code);
    }

    /**
     * Insert user verifications.
     *
     * @param $payload
     * @return int return the id of the inserted verification
     */
    public function insert($payload): int
    {
        $this->db->insert('verifications', [
            'user_id' => $payload['userId'],
            'code' => $payload['code'],
            'type' => $payload['type'],
            'expired_at' => $payload['expiredAt'],
        ]);

        return $this->db->insertId();
    }

    /**
     * Delete verification by type and user id.
     *
     * @param string $type
     * @param int $userId
     * @return mixed
     * @throws MeekroDBException
     */
    public function deleteByTypeAndUser(string $type, int $userId): mixed
    {
        return $this->db->delete('verifications', 'type = %s and user_id = %d', $type, $userId);
    }
}
