<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use Booking\Exception\BadRequestException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EditBookController extends BaseBookController
{
    /**
     * @param int $id
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws BadRequestException
     * @throws UnauthorizedException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->getPayloadCreateOrEdit($req, true);
        $data['book_id'] = $id;
        $this->book->edit($data);

        return $this->sendJson(null, StatusCode::STATUS_CREATED, 'Book created successfully.');
    }
}
