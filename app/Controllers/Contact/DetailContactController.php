<?php

declare(strict_types=1);

namespace App\Controllers\Contact;

use Booking\Exception\NotFoundException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;

class DetailContactController extends BaseContactController
{
    /**
     * @param int $id
     * @return ResponseInterface
     * @throws UnprocessableEntitiesException
     * @throws NotFoundException
     */
    public function __invoke(int $id): ResponseInterface
    {
        $contact = $this->contact->getDetail($id);

        return $this->sendJson($contact, StatusCode::STATUS_OK, 'Get contact detail successfully.');
    }
}
