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

class CreateCategoryController extends BaseCategoryController
{
    /**
     * @throws UnprocessableEntitiesException
     * @throws BadRequestException
     * @throws UnauthorizedException
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $id = AuthService::getUserIdFromToken($req);
        $data = $this->parseRequestDataToArray($req);

        $validation = v::key('name', v::stringType()->length(5, 50))
            ->key('slug', v::stringType()->length(5, 50));

        $validation->assert($data);
        $data['created_by'] = $id;
        $this->category->insert($data);

        return $this->sendJson(null, StatusCode::STATUS_CREATED, 'Category created successfully.');
    }
}
