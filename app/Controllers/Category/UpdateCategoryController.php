<?php

declare(strict_types=1);

namespace App\Controllers\Category;

use App\Services\AuthService;
use Booking\Exception\BadRequestException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class UpdateCategoryController extends BaseCategoryController
{

    /**
     * @throws UnprocessableEntitiesException
     * @throws UnauthorizedException
     * @throws BadRequestException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $userId = AuthService::getUserIdFromToken($req);
        $data = $this->parseRequestDataToArray($req);

        $validation = v::key('name', v::stringType()->length(5, 50))
            ->key('slug', v::stringType()->length(5, 50));

        $validation->assert($data);
        $data['category_id'] = $id;
        $data['updated_by'] = $userId;
        $this->category->update($data);

        return $this->sendJson(null, StatusCode::STATUS_ACCEPTED, 'Category updated successfully.');
    }
}
