<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\BaseRepository;
use MeekroDBException;

class CategoryRepository extends BaseRepository
{
    /**
     * Get all categories.
     *
     * @return mixed
     */
    public function getAll(): mixed
    {
        return $this->db->query('SELECT * FROM categories');
    }

    /**
     * Get category by id.
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        return $this->db->queryFirstRow('SELECT * FROM categories where category_id = %s', $id);
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
     * @param int $categoryId
     * @return int
     * @throws MeekroDBException
     */
    public function delete($payload): int
    {
        $this->db->update('categories', [
            'deleted_by' => $payload['deleted_by'],
            'deleted_at' => date('Y-m-d H:i:s'),
        ], 'category_id = %s', $payload['category_id']);

        return $this->db->affectedRows();
    }
}
