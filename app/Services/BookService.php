<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Booking\Constants as CoreConstants;
use Booking\Exception\BadRequestException;
use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Repository\BaseRepository;
use MeekroDB;
use Throwable;

class BookService
{
    /** @var MeekroDB */
    protected MeekroDB $repo;

    /** @var BookRepository */
    protected BookRepository $book;

    /** @var CategoryRepository */
    protected CategoryRepository $category;

    /**
     * @param BaseRepository $repo
     */
    public function __construct(BaseRepository $repo)
    {
        $this->repo = $repo->db();
        $this->book = new BookRepository($this->repo);
        $this->category = new CategoryRepository($this->repo);
    }

    /**
     * Mapping category response.
     *
     * @param $book
     * @param string $type
     * @return array
     */
    public static function response($book, string $type = 'all'): array
    {
        $data = [
            'bookId' => (int)$book['book_id'],
            'title' => $book['title'],
            'slug' => $book['slug'],
            'image' => config('app.url') . $book['image'],
            'rating' => (float)$book['rating'],
        ];

        if (in_array($type, ['all', 'detail'])) {
            $data['language'] = $book['language'];
            $data['publishedAt'] = $book['published_at'];
            $data['createdAt'] = $book['created_at'];
            $data['deletedAt'] = $book['deleted_at'];
        }

        if (in_array($type, ['detail', 'card'])) {
            $data['author'] = json_decode($book['author']);
        }

        if ($type === 'detail') {
            $data['isbn'] = $book['isbn'];
            $data['sku'] = $book['sku'];
            $data['pages'] = (int)$book['pages'];
            $data['weight'] = (float)$book['weight'];
            $data['width'] = (int)$book['width'];
            $data['height'] = (int)$book['height'];
            $data['description'] = $book['description'];
            $data['createdBy'] = (int)$book['created_by'];
            $data['updatedAt'] = $book['updated_at'];
            $data['updatedBy'] = (int)$book['updated_by'];
            $data['deletedBy'] = (int)$book['deleted_by'];
        }

        if (isset($book['category'])) {
            $data['category'] = collect($book['category'])->map(function ($category) {
                return [
                    'categoryId' => (int)$category['category_id'],
                    'name' => $category['name'],
                    'slug' => $category['slug'],
                ];
            })->toArray();
        }

        return $data;
    }

    /**
     * Get category by id.
     *
     * @param int $id
     * @return array
     * @throws NotFoundException
     * @throws UnprocessableEntitiesException
     */
    public function getDetail(int $id): array
    {
        try {
            $data = $this->book->getById($id);
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get all book.');
        }

        if (!$data) {
            throw new NotFoundException('Book not found.');
        }

        try {
            $category = $this->book->getCategoryByBookId($id);
            $data['category'] = $category;
        } catch (Throwable $t) {
            errorLog($t);
        }

        return self::response($data, 'detail');
    }


    /**
     * Get category by id.
     *
     * @param int $id
     * @return array
     * @throws UnprocessableEntitiesException
     */
    public function getRecommendation(int $id): array
    {
        try {
            $data = $this->book->getRecommendationByBookId($id, 6);
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get book recommendation.');
        }

        return collect($data)->map(fn($book) => self::response($book, 'card'))->toArray();
    }

    /**
     * Get category by id.
     *
     * @param int $id
     * @return array
     * @throws UnprocessableEntitiesException
     */
    public function getStock(int $id): array
    {
        try {
            $data = $this->book->getStock($id);
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get stock book.');
        }

        return [
            'stock' => (int)$data['stock'],
            'borrow' => (int)$data['borrow'],
        ];
    }

    /**
     * Get filter
     *
     * @return array
     * @throws UnprocessableEntitiesException
     */
    public function getFilter(): array
    {
        try {
            $published = $this->book->getPublishedYear();
            $category = $this->category->getAllActive();
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get book filter.');
        }

        return [
            'year' => collect($published)->map(fn($year) => (int)$year['year'])->toArray(),
            'category' => collect($category)->map(fn($category) => [
                'categoryId' => (int)$category['category_id'],
                'name' => $category['name'],
                'slug' => $category['slug'],
            ])->toArray(),
        ];
    }

    /**
     * @param $payload
     * @return void
     * @throws BadRequestException
     * @throws UnprocessableEntitiesException
     */
    public function create($payload): void
    {
        try {
            $this->repo->startTransaction();
            $bookId = $this->book->insert($payload);
            $this->book->insertCategories($bookId, $payload['categoryId']);
            $this->repo->commit();
        } catch (Throwable $t) {
            $this->repo->rollback();
            removeFile($payload['image']);
            errorLog($t);

            infoLog($t->getCode() . '');

            if ($t->getCode() === 1062) {
                $message = $t->getMessage();
                $error = [];
                if (str_contains($message, 'books.isbn')) {
                    $error = [
                        'isbn' => 'ISBN already exists.',
                    ];
                }
                if (str_contains($message, 'books.sku')) {
                    $error = [
                        'sku' => 'SKU already exists.',
                    ];
                }
                if (str_contains($message, 'books.slug')) {
                    $error = [
                        'slug' => 'Slug already exists.',
                    ];
                }

                throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, $error);
            }

            if ($t->getCode() === 1452) {
                throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                    'categoryId' => 'Invalid category id.',
                ]);
            }

            throw new UnprocessableEntitiesException('Book could not be created, please contact administrator.');
        }
    }

    /**
     * Update book stock.
     *
     * @param $payload
     * @return void
     * @throws BadRequestException
     * @throws UnprocessableEntitiesException
     */
    public function update($payload): void
    {
        try {
            $this->book->updateStock($payload);
        } catch (Throwable $e) {
            errorLog($e);

            if ($e->getCode() === 1644) {
                throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                    'stock' => $e->getMessage(),
                ]);
            }

            throw new UnprocessableEntitiesException('Book stock could not be updated, please try again later.');
        }
    }

    /**
     * Delete book.
     *
     * @param $payload
     * @param bool $isRestore
     * @return void
     * @throws UnprocessableEntitiesException
     */
    public function delete($payload, bool $isRestore = false): void
    {
        try {
            if ($isRestore) {
                $this->book->restoreDelete($payload);
            } else {
                $this->book->delete($payload);
            }
        } catch (Throwable $e) {
            errorLog($e);

            $state = $isRestore ? 'restored' : 'deleted';
            throw new UnprocessableEntitiesException("Book could not be $state, please try again later.");
        }
    }
}
