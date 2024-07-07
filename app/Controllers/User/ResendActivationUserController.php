<?php

declare(strict_types=1);

namespace App\Controllers\User;

use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ResendActivationUserController extends BaseUserController
{
    /**
     * @param int $id
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $this->user->resendVerification($id);
        return $this->sendJson(null, StatusCode::STATUS_OK, 'Resend email verification to user successfully.');
    }
}
