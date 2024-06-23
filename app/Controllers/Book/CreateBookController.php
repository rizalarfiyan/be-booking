<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use Booking\Exception\BadRequestException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateBookController extends BaseBookController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws UnauthorizedException
     * @throws BadRequestException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->getPayloadCreateOrEdit($req);
        $this->book->create($data);

        return $this->sendJson(null, StatusCode::STATUS_CREATED, 'Book created successfully.');
    }
}
