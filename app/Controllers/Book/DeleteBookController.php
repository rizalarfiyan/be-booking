<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use App\Services\AuthService;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DeleteBookController extends BaseBookController
{
    /**
     * @throws UnauthorizedException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $userId = AuthService::getUserIdFromToken($req);
        $data = $this->parseRequestDataToArray($req);

        $isRestore = $data['isRestore'] ?? false;
        $data['book_id'] = $id;
        $data['deleted_by'] = $userId;
        $data['updated_by'] = $userId;
        $this->book->delete($data, $isRestore);

        $state = $isRestore ? 'restored' : 'deleted';

        return $this->sendJson(null, StatusCode::STATUS_OK, "Book $state successfully.");
    }
}
