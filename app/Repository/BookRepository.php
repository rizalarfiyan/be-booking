<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\BaseRepository;

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
}
