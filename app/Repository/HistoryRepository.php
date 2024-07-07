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
        if (!empty($payload['search'])) {
            $where->add('b.title like %s', "%{$payload['search']}%");
        }

        if (!empty($payload['userId']) && !$payload['isAdmin']) {
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

        return (int)$this->db->queryFirstField('SELECT COUNT(history_id) FROM histories h JOIN books b USING (book_id) WHERE %l', $condition) ?? 0;
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
            'borrow_at' => datetime(),
            'created_by' => $payload['createdBy'],
        ]);

        return $this->db->insertId();
    }

    /**
     *
     * @param $payload
     * @return int
     * @throws MeekroDBException
     */
    public function cancel($payload): int
    {
        $this->db->update('histories', [
            'status' => 'cancel',
        ], 'history_id = %d', $payload['historyId']);

        return $this->db->affectedRows();
    }

    /**
     *
     * @param $payload
     * @return int
     * @throws MeekroDBException
     */
    public function read($payload): int
    {
        $this->db->update('histories', [
            'status' => 'read',
            'return_at' => datetime()->addDays(7),
        ], 'history_id = %d', $payload['historyId']);

        return $this->db->affectedRows();
    }

    /**
     * Get category by id.
     *
     * @param int $id
     * @return mixed
     */
    public function getBorrowTime(int $id): mixed
    {
        return $this->db->queryFirstRow('SELECT borrow_at FROM histories WHERE history_id = %d', $id);
    }

    /**
     *
     * @param $payload
     * @return int
     * @throws MeekroDBException
     */
    public function return($payload): int
    {
        $this->db->update('histories', [
            'status' => 'read',
            'return_at' => datetime()->addDays(7),
        ], 'history_id = %d', $payload['historyId']);

        return $this->db->affectedRows();
    }

    /**
     *
     * @param $payload
     * @return int
     * @throws MeekroDBException
     */
    public function returned($payload): int
    {
        $this->db->update('histories', [
            'status' => 'success',
            'point' => $payload['point'],
            'returned_at' => datetime(),
            'returned_by' => $payload['returnedBy'],
        ], 'history_id = %d', $payload['historyId']);

        return $this->db->affectedRows();
    }

    /**
     * Get rating history by id.
     *
     * @param int $id
     * @return mixed
     */
    public function getReviewHistory(int $id): mixed
    {
        return $this->db->queryFirstRow('SELECT history_id, rating, review, created_at, updated_at FROM rating_histories WHERE history_id = %d', $id);
    }

    /**
     *
     * @param $payload
     * @return void
     */
    public function review($payload): void
    {
        $this->db->query('INSERT INTO rating_histories (history_id, rating, review) VALUES (%d, %d, %s) ON DUPLICATE KEY UPDATE rating = %d, review = %s', $payload['historyId'], $payload['rating'], $payload['review'], $payload['rating'], $payload['review']);
    }
}
