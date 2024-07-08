<?php

declare(strict_types=1);

namespace App\Controllers\History;

use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReviewHistoryController extends BaseHistoryController
{
    /**
     * @param int $id
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->history->reviewHistory($id);

        return $this->sendJson($data, StatusCode::STATUS_OK, 'Get review history detail successfully.');
    }
}
