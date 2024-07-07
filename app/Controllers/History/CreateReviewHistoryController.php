<?php

declare(strict_types=1);

namespace App\Controllers\History;

use Booking\Message\StatusCodeInterface as StatusCode;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class CreateReviewHistoryController extends BaseHistoryController
{
    /**
     * Create a review for a history.
     *
     * @param int $id
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws Exception
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $data = $this->parseRequestDataToArray($req);
        $validation = v::key('rating', v::floatVal()->min(0)->max(5))
            ->key('review', v::stringType());
        $validation->assert($data);

        $data['historyId'] = $id;
        $this->history->createReview($data);

        return $this->sendJson(null, StatusCode::STATUS_CREATED, 'Create or update review history successfully.');
    }
}
