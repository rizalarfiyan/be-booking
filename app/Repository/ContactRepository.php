<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\BaseRepository;
use WhereClause;

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
            'first_name' => $payload['firstName'],
            'last_name' => $payload['lastName'] ?? '',
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'message' => $payload['message'],
        ]);

        return $this->db->insertId();
    }

    /**
     * @param $payload
     * @return WhereClause
     */
    protected function baseGetAll($payload): WhereClause
    {
        $where = new WhereClause('or');
        if (! empty($payload['search'])) {
            $where->add('first_name like %s', "%{$payload['search']}%");
            $where->add('last_name like %s', "%{$payload['search']}%");
            $where->add('email like %s', "%{$payload['search']}%");
        }

        return $where;
    }

    /**
     * @param $payload
     * @return mixed
     */
    public function getAll($payload): mixed
    {
        $condition = $this->baseGetAll($payload);
        $orderBy = columnValidation([
            'contact_id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'message',
            'is_read',
            'created_at',
            'updated_at',
        ], $payload['orderType']) ?? 'created_at';
        $orderType = columnValidation(['ASC', 'DESC'], $payload['orderType']) ?? 'ASC';

        return $this->db->query('SELECT * FROM contacts WHERE %l ORDER BY %l %l LIMIT %d OFFSET %d', $condition, $orderBy, $orderType, $payload['count'], $payload['page'] * $payload['count']);
    }

    /**
     * @param $payload
     * @return int
     */
    public function countAll($payload): int
    {
        $condition = $this->baseGetAll($payload);

        return (int) $this->db->queryFirstField('SELECT COUNT(*) FROM contacts WHERE %l', $condition) ?? 0;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        return $this->db->queryFirstRow('SELECT * FROM contacts where contact_id = %d', $id);
    }
}
