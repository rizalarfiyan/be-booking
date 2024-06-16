<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\CategoryRepository;
use Booking\Constants as CoreConstants;
use Booking\Exception\BadRequestException;
use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Repository\BaseRepository;
use MeekroDB;
use MeekroDBException;
use Throwable;

class CategoryService
{
    /** @var MeekroDB */
    protected MeekroDB $repo;

    /** @var CategoryRepository */
    protected CategoryRepository $category;

    /**
     * @param BaseRepository $repo
     */
    public function __construct(BaseRepository $repo)
    {
        $this->repo = $repo->db();
        $this->category = new CategoryRepository($this->repo);
    }

    /**
     * Mapping category response.
     *
     * @param $category
     * @param bool $idDetail
     * @return array
     */
    public static function response($category, bool $idDetail = false): array
    {
        $data = [
            'categoryId' => (int) $category['category_id'],
            'name' => $category['name'],
            'slug' => $category['slug'],
            'createdAt' => $category['created_at'],
            'deletedAt' => $category['deleted_at'],
        ];

        if ($idDetail) {
            $data['createdBy'] = $category['created_by'];
            $data['updatedAt'] = $category['updated_at'];
            $data['updatedBy'] = $category['updated_by'];
            $data['deletedBy'] = $category['deleted_by'];
        }

        return $data;
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
                'content' => collect($this->category->getAll($payload))->map(fn ($contact) => self::response($contact)),
                'total' => $this->category->countAll($payload),
            ];
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get all contacts.');
        }
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
            $data = $this->category->getById($id);
        } catch (Throwable $t) {
            errorLog($t);
            throw new NotFoundException('Failed to get all category.');
        }

        if (! $data) {
            throw new UnprocessableEntitiesException('Category not found.');
        }

        return self::response($data, true);
    }

    /**
     * Insert category.
     *
     * @param $payload
     * @return void
     * @throws BadRequestException
     * @throws UnprocessableEntitiesException
     */
    public function insert($payload): void
    {
        try {
            $this->category->insert($payload);
        } catch (Throwable $e) {
            errorLog($e);

            if ($e->getCode() === 1062) {
                throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                    'slug' => 'Slug already exists.',
                ]);
            }

            throw new UnprocessableEntitiesException('Category could not be created, please try again later.');
        }
    }

    /**
     * Update category.
     *
     * @param $payload
     * @return void
     * @throws BadRequestException
     * @throws UnprocessableEntitiesException
     */
    public function update($payload): void
    {
        try {
            $this->category->update($payload);
        } catch (Throwable $e) {
            errorLog($e);

            if ($e->getCode() === 1062) {
                throw new BadRequestException(CoreConstants::VALIDATION_MESSAGE, [
                    'slug' => 'Slug already exists.',
                ]);
            }

            throw new UnprocessableEntitiesException('Category could not be updated, please try again later.');
        }
    }

    /**
     * Delete category.
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
                $this->category->restoreDelete($payload);
            } else {
                $this->category->delete($payload);
            }
        } catch (Throwable $e) {
            errorLog($e);

            $state = $isRestore ? 'restored' : 'deleted';
            throw new UnprocessableEntitiesException("Category could not be $state, please try again later.");
        }
    }
}
