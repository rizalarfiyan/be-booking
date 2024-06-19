<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\BaseRepository;
use MeekroDBException;

class BookRepository extends BaseRepository
{
    /**
     * Insert book.
     *
     * @param $payload
     * @return int return the id of the inserted user
     */
    public function insert($payload): int
    {
        $this->db->insert('books', [
            'isbn' => $payload['isbn'],
            'sku' => $payload['sku'],
            'author' => json_encode($payload['author']),
            'title' => $payload['title'],
            'slug' => $payload['slug'],
            'pages' => $payload['pages'],
            'weight' => $payload['weight'],
            'height' => $payload['height'],
            'width' => $payload['width'],
            'image' => $payload['image'],
            'language' => $payload['language'],
            'description' => $payload['description'],
            'published_at' => $payload['publishedAt'],
            'created_by' => $payload['createdBy'],
            'updated_by' => $payload['updatedBy'],
        ]);

        return $this->db->insertId();
    }

    /**
     * @param $bookId
     * @param $categoryIds
     * @return void
     */
    public function insertCategories($bookId, $categoryIds): void
    {
        $bookCategories = collect($categoryIds)->map(function ($categoryId) use ($bookId) {
            return [
                'book_id' => $bookId,
                'category_id' => $categoryId,
            ];
        })->toArray();

        $this->db->insert('book_categories', $bookCategories);
    }

    /**
     * Delete books.
     *
     * @param $payload
     * @return int
     * @throws MeekroDBException
     */
    public function delete($payload): int
    {
        $this->db->update('books', [
            'updated_by' => $payload['updated_by'],
            'deleted_by' => $payload['deleted_by'],
            'deleted_at' => datetime(),
        ], 'book_id = %s', $payload['book_id']);

        return $this->db->affectedRows();
    }

    /**
     * Delete books.
     *
     * @param $payload
     * @return int
     * @throws MeekroDBException
     */
    public function restoreDelete($payload): int
    {
        $this->db->update('books', [
            'updated_by' => $payload['updated_by'],
            'deleted_by' => null,
            'deleted_at' => null,
        ], 'book_id = %s', $payload['book_id']);

        return $this->db->affectedRows();
    }

    /**
     * Get book by id.
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id): mixed
    {
        // TODO: update the query
        return $this->db->queryFirstRow('SELECT * FROM books WHERE book_id = %s', $id);
    }

    /**
     * Get book category by book id.
     *
     * @param int $id
     * @return mixed
     */
    public function getCategoryByBookId(int $id): mixed
    {
        $query = 'WITH bcs AS (
            SELECT category_id from book_categories where book_id = %s
        )
        SELECT c.category_id, c.name, c.slug FROM bcs
        JOIN categories c USING (category_id)
        WHERE c.deleted_at IS NULL';

        return $this->db->query($query, $id);
    }

    /**
     * Get book published year.
     *
     * @return mixed
     */
    public function getPublishedYear(): mixed
    {
        return $this->db->query('SELECT DISTINCT YEAR(published_at) as year FROM books WHERE deleted_at IS NULL ORDER BY year DESC');
    }

    /**
     * Update book stock.
     *
     * @param $payload
     * @return int
     * @throws MeekroDBException
     */
    public function updateStock($payload): int
    {
        $this->db->update('books', [
            'stock' => $payload['stock'],
            'updated_by' => $payload['updated_by'],
        ], 'book_id = %s', $payload['book_id']);

        return $this->db->affectedRows();
    }
}
