<?php

declare(strict_types=1);

namespace App\Controllers\Contact;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class PostNewContact extends BaseContactController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);

        $validation = v::key('email', v::email()->noWhitespace())
            ->key('first_name', v::alpha()->length(3, 50))
            ->key('last_name', v::optional(v::alpha()->length(3, 50)))
            ->key('phone', v::stringType()->numericVal()->length(10, 20))
            ->key('message', v::stringType()->length(10, 500));

        $validation->assert($data);
        $this->contact->newContact($data);

        return $this->sendJson(null, StatusCode::STATUS_OK, 'Send contact successfully.');
    }
}
