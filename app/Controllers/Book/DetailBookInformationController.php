<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DetailBookInformationController extends BaseBookController
{
    /**
     * @throws NotFoundException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(string $slug, ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->book->getDetailInformation($slug);

        return $this->sendJson($data, StatusCode::STATUS_OK, 'Book Successfully Requested.');
    }
}
