<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants;
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

    public static function response($category): array
    {
        return [
            'categoryId' => $category['category_id'],
            'name' => $category['name'],
            'slug' => $category['slug'],
        ];
    }

    /**
     * Get all categories.
     *
     * @return mixed
     */
    public function getAll(): mixed
    {
        return $this->category->getAll();
    }

    /**
     * Get category by id.
     *
     * @param int $id
     * @return mixed
     * @throws NotFoundException
     */
    public function getById(int $id): mixed
    {
        $data = $this->category->getById($id);

        if (! $data) {
            throw new NotFoundException('Category not found');
        }

        return self::response($data);
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

            throw new UnprocessableEntitiesException('Category could not be created, please contact Rizal.');
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
                    'category' => 'Category already exists.',
                ]);
            }

            throw new UnprocessableEntitiesException('Category could not be updated, please contact Rizal.');
        }
    }

    /**
     * Delete category.
     *
     * @param int $id
     * @return void
     * @throws MeekroDBException
     */
    public function delete($payload): void
    {
        $this->category->delete($payload);
    }
}
