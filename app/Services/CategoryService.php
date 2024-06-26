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
     * @param bool $isDetail
     * @return array
     */
    public static function response($category, bool $isDetail = false): array
    {
        $data = [
            'categoryId' => (int) $category['category_id'],
            'name' => $category['name'],
            'slug' => $category['slug'],
            'createdAt' => $category['created_at'],
            'deletedAt' => $category['deleted_at'],
        ];

        if ($isDetail) {
            $data['createdBy'] = (int) $category['created_by'];
            $data['updatedAt'] = $category['updated_at'];
            $data['updatedBy'] = (int) $category['updated_by'];
            $data['deletedBy'] = (int) $category['deleted_by'];
        }

        return $data;
    }

    /**
     * @param $category
     * @return array
     */
    public static function dropdownResponse($category): array
    {
        return [
            'value' => (int) $category['category_id'],
            'label' => $category['name'],
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
                'content' => collect($this->category->getAll($payload))->map(fn ($contact) => self::response($contact)),
                'total' => $this->category->countAll($payload),
            ];
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get all categories.');
        }
    }

    /**
     * Get all categories.
     *
     * @param $payload
     * @return array
     * @throws UnprocessableEntitiesException
     */
    public function getAllDropdown($payload): array
    {
        try {
            return collect($this->category->getAllDropdown($payload))->map(fn ($contact) => self::dropdownResponse($contact))->toArray();
        } catch (Throwable $t) {
            errorLog($t);
            throw new UnprocessableEntitiesException('Failed to get all categories.');
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
            throw new UnprocessableEntitiesException('Failed to get all category.');
        }

        if (! $data) {
            throw new NotFoundException('Category not found.');
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
