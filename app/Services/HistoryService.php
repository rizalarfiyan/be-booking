<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\HistoryRepository;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Repository\BaseRepository;
use MeekroDB;
use Throwable;

class HistoryService
{
    /** @var MeekroDB */
    protected MeekroDB $repo;

    /** @var HistoryRepository */
    protected HistoryRepository $history;

    /**
     * @param BaseRepository $repo
     */
    public function __construct(BaseRepository $repo)
    {
        $this->repo = $repo->db();
        $this->history = new HistoryRepository($this->repo);
    }

    /**
     * Mapping category response.
     *
     * @param $history
     * @return array
     */
    public static function response($history): array
    {
        return [
            'historyId' => (int)$history['history_id'],
            'userId' => (int)$history['user_id'],
            'title' => $history['title'],
            'status' => $history['status'],
            'point' => (int)$history['point'],
            'createdAt' => $history['created_at'],
            'returnAt' => $history['return_at'],
            'borrowAt' => $history['borrow_at'],
            'returnedAt' => $history['returned_at'],
        ];
    }

    /**
     * Get all categories.
     *
     * @param $payload
     * @return array
     * @throws UnprocessableEntitiesException
     */
    public function getAll($payload): array
    {
        try {
            return [
                'content' => collect($this->history->getAll($payload))->map(fn($data) => self::response($data)),
                'total' => $this->history->countAll($payload),
            ];
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get all history.');
        }
    }

    /**
     * Borrow the book.
     *
     * @param $payload
     * @return void
     * @throws UnprocessableEntitiesException
     */
    public function borrow($payload): void
    {
        try {
            $this->history->insert($payload);
        } catch (Throwable $e) {
            errorLog($e);

            if ($e->getCode() === 1644) {
                throw new UnprocessableEntitiesException($e->getMessage());
            }

            throw new UnprocessableEntitiesException('Cannot borrow the book, please try again later.');
        }
    }


    /**
     * Borrow the book.
     *
     * @param $payload
     * @return void
     * @throws UnprocessableEntitiesException
     */
    public function cancel($payload): void
    {
        try {
            $this->history->cancel($payload);
        } catch (Throwable $e) {
            errorLog($e);

            if ($e->getCode() === 1644) {
                throw new UnprocessableEntitiesException($e->getMessage());
            }

            throw new UnprocessableEntitiesException('Cannot change status to cancel, please try again later.');
        }
    }


    /**
     * Borrow the book.
     *
     * @param $payload
     * @return void
     * @throws UnprocessableEntitiesException
     */
    public function read($payload): void
    {
        try {
            $this->history->read($payload);
        } catch (Throwable $e) {
            errorLog($e);

            if ($e->getCode() === 1644) {
                throw new UnprocessableEntitiesException($e->getMessage());
            }

            throw new UnprocessableEntitiesException('Cannot change status to read, please try again later.');
        }
    }

    /**
     * Borrow the book.
     *
     * @param $payload
     * @return void
     * @throws UnprocessableEntitiesException
     */
    public function return($payload): void
    {
        try {
            $borrowAt = $this->history->getBorrowTime($payload['historyId']);
            $payload['point'] = !$borrowAt ? 1 : (datetime($borrowAt['borrow_at'])->diffInDays(datetime()) + 1) * 2;
            $this->history->returned($payload);
        } catch (Throwable $e) {
            errorLog($e);

            if ($e->getCode() === 1644) {
                throw new UnprocessableEntitiesException($e->getMessage());
            }

            throw new UnprocessableEntitiesException('Cannot change status to return, please try again later.');
        }
    }
}
