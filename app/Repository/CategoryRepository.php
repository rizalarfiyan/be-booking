<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\BaseRepository;
use MeekroDBException;
use WhereClause;

class CategoryRepository extends BaseRepository
{
    /**
     * @param $payload
     * @return WhereClause
     */
    protected function baseGetAll($payload): WhereClause
    {
        $where = new WhereClause('or');
        if (! empty($payload['search'])) {
            $where->add('name like %s', "%{$payload['search']}%");
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
            'category_id',
            'name',
            'slug',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'deleted_at',
            'deleted_by',
        ], $payload['orderType']) ?? 'created_at';
        $orderType = columnValidation(['ASC', 'DESC'], $payload['orderType']) ?? 'ASC';

        return $this->db->query('SELECT * FROM categories WHERE %l ORDER BY %l %l LIMIT %d OFFSET %d', $condition, $orderBy, $orderType, $payload['count'], $payload['page'] * $payload['count']);
    }

    /**
     * @param $payload
     * @return int
     */
    public function countAll($payload): int
    {
        $condition = $this->baseGetAll($payload);

        return (int) $this->db->queryFirstField('SELECT COUNT(*) FROM categories WHERE %l', $condition) ?? 0;
    }

    /**
     * Get category by id.
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        return $this->db->queryFirstRow('SELECT * FROM categories WHERE category_id = %s', $id);
    }

    /**
     * Insert category.
     *
     * @param $payload
     * @return int return the id of the inserted category
     */
    public function insert($payload): int
    {
        $this->db->insert('categories', [
            'name' => $payload['name'],
            'slug' => $payload['slug'],
            'created_by' => $payload['created_by'],
        ]);

        return $this->db->insertId();
    }

    /**
     * Update category.
     *
     * @param $payload
     * @return int
     * @throws MeekroDBException
     */
    public function update($payload): int
    {
        $this->db->update('categories', [
            'name' => $payload['name'],
            'slug' => $payload['slug'],
            'updated_by' => $payload['updated_by'],
        ], 'category_id = %s', $payload['category_id']);

        return $this->db->affectedRows();
    }

    /**
     * Delete category.
     *
     * @param $payload
     * @return int
     * @throws MeekroDBException
     */
    public function delete($payload): int
    {
        $this->db->update('categories', [
            'deleted_by' => $payload['deleted_by'],
            'deleted_at' => datetime(),
        ], 'category_id = %s', $payload['category_id']);

        return $this->db->affectedRows();
    }
}
