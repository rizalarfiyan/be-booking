<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use App\Services\AuthService;
use Booking\Exception\BadRequestException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class UpdateStockController extends BaseBookController
{
    /**
     * @throws UnprocessableEntitiesException
     * @throws UnauthorizedException
     * @throws BadRequestException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $userId = AuthService::getUserIdFromToken($req);
        $data = $this->parseRequestDataToArray($req);

        $validation = v::key('stock', v::intVal());

        $validation->assert($data);
        $data['book_id'] = $id;
        $data['updated_by'] = $userId;
        $this->book->update($data);

        return $this->sendJson(null, StatusCode::STATUS_ACCEPTED, 'Update book stock successfully.');
    }
}
