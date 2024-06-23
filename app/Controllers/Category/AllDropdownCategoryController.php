<?php

declare(strict_types=1);

namespace App\Controllers\Category;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AllDropdownCategoryController extends BaseCategoryController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $metadata = $this->getDatatable($req);
        $categories = $this->category->getAllDropdown($metadata);

        return $this->sendJson($categories, StatusCode::STATUS_OK, 'Get all category successfully.');
    }
}
