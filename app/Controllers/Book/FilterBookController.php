<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FilterBookController extends BaseBookController
{
    /**
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->book->getFilter();

        return $this->sendJson($data, StatusCode::STATUS_OK, 'Get Book Filter Successfully.');
    }
}
