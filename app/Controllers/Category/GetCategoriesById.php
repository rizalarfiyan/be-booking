<?php

declare(strict_types=1);

namespace App\Controllers\Category;

use Booking\Exception\NotFoundException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetCategoriesById extends BaseCategoryController
{
    /**
     * @throws NotFoundException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $categoryID = $id;
        $data = $this->category->getById((int) $categoryID);

        return $this->sendJson($data, StatusCode::STATUS_OK, 'Category Successfully Requested.');
    }
}
