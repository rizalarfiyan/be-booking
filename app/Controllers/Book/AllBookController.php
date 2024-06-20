<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use App\Controllers\Category\BaseCategoryController;
use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AllBookController extends BaseBookController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $metadata = $this->getDatatable($req);
        $categories = $this->book->getAll($metadata);

        return $this->sendJson($this->listResponse($categories, $metadata), StatusCode::STATUS_OK, 'Get all category successfully.');
    }
}
