<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\BookRepository;
use Booking\Constants as CoreConstants;
use Booking\Exception\BadRequestException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Repository\BaseRepository;
use MeekroDB;
use Exception;

class BookService
{
    /** @var MeekroDB */
    protected MeekroDB $repo;

    /** @var BookRepository */
    protected BookRepository $book;

    /**
     * @param BaseRepository $repo
     */
    public function __construct(BaseRepository $repo)
    {
        $this->repo = $repo->db();
        $this->book = new BookRepository($this->repo);
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
        } catch (Exception $e) {
            $this->repo->rollback();
            removeFile($payload['image']);
            errorLog($e);

            infoLog($e->getCode()."");

            if ($e->getCode() === 1062) {
                $message = $e->getMessage();
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

            if ($e->getCode() === 1452) {
                throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                    'categoryId' => 'Invalid category id.',
                ]);
            }

            throw new UnprocessableEntitiesException('Book could not be created, please contact administrator.');
        }
    }
}
