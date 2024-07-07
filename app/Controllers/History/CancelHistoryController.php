<?php

declare(strict_types=1);

namespace App\Controllers\History;

use App\Constants;
use App\Services\AuthService;
use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class CancelHistoryController extends BaseHistoryController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);
        $validation = v::key('historyId', v::intVal());
        $validation->assert($data);
        $this->history->cancel($data);

        return $this->sendJson(null, StatusCode::STATUS_OK, 'Cancel a book successfully.');
    }
}
