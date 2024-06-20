<?php

declare(strict_types=1);

namespace App\Repository;

use Booking\Repository\BaseRepository;
use MeekroDBException;
use WhereClause;

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
        ], 'book_id = %d', $payload['book_id']);

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
        ], 'book_id = %d', $payload['book_id']);

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
        return $this->db->queryFirstRow('SELECT * FROM books WHERE book_id = %d', $id);
    }


    /**
     * Get book stock.
     *
     * @param int $id
     * @return mixed
     */
    public function getStock(int $id): mixed
    {
        return $this->db->queryFirstRow('SELECT stock, borrowed FROM books WHERE book_id = %d', $id);
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
            SELECT category_id from book_categories where book_id = %d
        )
        SELECT c.category_id, c.name, c.slug FROM bcs
        JOIN categories c USING (category_id)
        WHERE c.deleted_at IS NULL';

        return $this->db->query($query, $id);
    }

    /**
     * Get book category by book id.
     *
     * @param int $id
     * @param int $limit
     * @return mixed
     */
    public function getRecommendationByBookId(int $id, int $limit): mixed
    {
        $query = 'SELECT b.book_id, b.slug, b.title, getBookRating(b.rating, b.rating_count) AS rating, b.image, b.author
    FROM book_categories bc JOIN books b USING (book_id)
    WHERE b.deleted_at IS NULL
      AND b.book_id != %d
      AND bc.category_id IN (SELECT category_id FROM book_categories WHERE book_id = %d)
    GROUP BY b.book_id
    ORDER BY b.borrowed_count DESC, rating DESC
    LIMIT %d';

        return $this->db->query($query, $id, $id, $limit);
    }

    /**
     * Get book published year.
     *
     * @return mixed
     */
    public function getPublishedYear(): mixed
    {
        return $this->db->query('SELECT DISTINCT YEAR(published_at) AS year FROM books WHERE deleted_at IS NULL ORDER BY YEAR DESC');
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

    /**
     * @param $payload
     * @return WhereClause
     */
    protected function baseGetList($payload): WhereClause
    {
        $where = new WhereClause('and');
        $where->add('b.deleted_at IS NULL');

        if (!empty($payload['year'])) {
            $where->add('YEAR(b.published_at) = %d', $payload['year']);
        }

        if (!empty($payload['search'])) {
            $lcSearch = strtolower($payload['search']);
            $orWhere = $where->addClause('or');
            $orWhere->add('b.title like %s', "%{$payload['search']}%");
            $orWhere->add("JSON_SEARCH(LOWER(JSON_UNQUOTE(b.author)), 'one', %s, NULL, '$[*]') IS NOT NULL", "%{$lcSearch}%");
        }

        if (!empty($payload['categoryId'])) {
            $where->add('bc.category_id = %d', $payload['categoryId']);
        }

        return $where;
    }

    /**
     * @param string $orderBy
     * @return string
     */
    protected function getListOrder(string $orderBy): string
    {
        switch ($orderBy) {
            case 'title':
                return 'b.title ASC';
            case 'rating':
                return 'b.rating DESC';
            case 'latest':
                return 'b.created_at DESC';
            default:
                return 'b.borrowed_count DESC';
        }
    }

    /**
     * @param $payload
     * @return mixed
     */
    public function getList($payload): mixed
    {
        $condition = $this->baseGetList($payload);
        $orderBy = columnValidation([
            'title',
            'popular',
            'rating',
            'latest',
        ], $payload['orderType']) ?? 'popular';

        $join = "";
        if (!empty($payload['categoryId'])) {
            $join = "JOIN book_categories bc USING (book_id)";
        }

        return $this->db->query('SELECT * FROM books b %l WHERE %l ORDER BY %l LIMIT %d OFFSET %d', $join, $condition, $this->getListOrder($orderBy), $payload['count'], $payload['page'] * $payload['count']);
    }

    /**
     * @param $payload
     * @return int
     */
    public function countList($payload): int
    {
        $condition = $this->baseGetList($payload);

        $join = "";
        if (!empty($payload['categoryId'])) {
            $join = "JOIN book_categories bc USING (book_id)";
        }
        return (int)$this->db->queryFirstField('SELECT COUNT(*) FROM books b %l WHERE %l', $join, $condition) ?? 0;
    }


    /**
     * @param $payload
     * @return WhereClause
     */
    protected function baseGetAll($payload): WhereClause
    {
        $where = new WhereClause('or');
        if (!empty($payload['search'])) {
            $where->add('b.title like %s', "%{$payload['search']}%");
            $where->add('b.author like %s', "%{$payload['search']}%");
            $where->add('b.isbn like %s', "%{$payload['search']}%");
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
        ], $payload['orderType']) ?? 'created_at';
        $orderType = columnValidation(['ASC', 'DESC'], $payload['orderType']) ?? 'ASC';
        return $this->db->query('SELECT * FROM books b WHERE %l ORDER BY %l %l LIMIT %d OFFSET %d', $condition, $orderBy, $orderType, $payload['count'], $payload['page'] * $payload['count']);
    }

    /**
     * @param $payload
     * @return int
     */
    public function countAll($payload): int
    {
        $condition = $this->baseGetList($payload);

        return (int)$this->db->queryFirstField('SELECT COUNT(*) FROM books b WHERE %l', $condition) ?? 0;
    }
}
