<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use App\Services\AuthService;
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
        $userId = AuthService::getUserIdFromTokenIfAvailable($req);
        $data = $this->book->getStock($id, $userId);

        return $this->sendJson($data, StatusCode::STATUS_OK, 'Get Book Stock Successfully.');
    }
}
