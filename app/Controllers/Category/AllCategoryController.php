<?php

declare(strict_types=1);

namespace App\Controllers\Category;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AllCategoryController extends BaseCategoryController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $query = $req->getQueryParams();
        $metadata = $this->getDatatable($req);
        $categories = $this->category->getAll([
            ...$metadata,
            'search' => $query['search'] ?? null,
        ]);

        return $this->sendJson($this->listResponse($categories, $metadata), StatusCode::STATUS_OK, 'Get all category successfully.');
    }
}
