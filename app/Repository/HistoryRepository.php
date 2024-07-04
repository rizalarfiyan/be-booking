<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\BaseRepository;
use MeekroDBException;
use WhereClause;

class HistoryRepository extends BaseRepository
{

    /**
     * @param $payload
     * @return WhereClause
     */
    protected function baseGetAll($payload): WhereClause
    {
        $where = new WhereClause('or');
        if (! empty($payload['search'])) {
            $where->add('b.title like %s', "%{$payload['search']}%");
        }

        if (! empty($payload['userId'])) {
            $where->add('h.user_id = %d', $payload['userId']);
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
            'created_at',
        ], $payload['orderType']) ?? 'h.created_at';
        $orderType = columnValidation(['ASC', 'DESC'], $payload['orderType']) ?? 'ASC';
        return $this->db->query('SELECT h.history_id, h.user_id, b.title, h.status, h.point, h.created_at, h.return_at, h.borrow_at, h.returned_at FROM histories h JOIN books b USING (book_id) WHERE %l ORDER BY %l %l LIMIT %d OFFSET %d', $condition, $orderBy, $orderType, $payload['count'], $payload['page'] * $payload['count']);
    }

    /**
     * @param $payload
     * @return int
     */
    public function countAll($payload): int
    {
        $condition = $this->baseGetAll($payload);

        return (int) $this->db->queryFirstField('SELECT count(history_id) FROM histories h JOIN books b USING (book_id) WHERE %l', $condition) ?? 0;
    }

        /**
     * Insert history.
     *
     * @param $payload
     * @return int return the id of the inserted history
     */
    public function insert($payload): int
    {
        $this->db->insert('histories', [
            'user_id' => $payload['userId'],
            'book_id' => $payload['bookId'],
            'borrow_by' => $payload['borrowBy'],
            'created_by' => $payload['createdBy'],
        ]);

        return $this->db->insertId();
    }
}
