<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class StockBookController extends BaseBookController
{
    /**
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->book->getStock($id);

        return $this->sendJson($data, StatusCode::STATUS_OK, 'Get Book Stock Successfully.');
    }
}
