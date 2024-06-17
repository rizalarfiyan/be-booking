<?php

declare(strict_types=1);

namespace App\Controllers\Contact;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class AllContactController extends BaseContactController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $query = $req->getQueryParams();
        $metadata = $this->getDatatable($req);
        $contacts = $this->contact->getAll([
            ...$metadata,
            'search' => $query['search'] ?? null,
        ]);

        return $this->sendJson($this->listResponse($contacts, $metadata), StatusCode::STATUS_OK, 'Get all contact successfully.');
    }
}
