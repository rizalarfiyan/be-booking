<?php

declare(strict_types=1);

namespace App\Controllers\Category;

use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DetailCategoryController extends BaseCategoryController
{
    /**
     * @throws NotFoundException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->category->getDetail($id);

        return $this->sendJson($data, StatusCode::STATUS_OK, 'Category Successfully Requested.');
    }
}
