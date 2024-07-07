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

class ReturnHistoryController extends BaseHistoryController
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

        $userId = AuthService::getUserIdFromToken($req);
        $data['returnedBy'] = $userId;
        $this->history->return($data);

        return $this->sendJson(null, StatusCode::STATUS_OK, 'Return a book successfully.');
    }
}
