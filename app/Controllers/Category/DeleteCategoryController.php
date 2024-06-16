<?php

declare(strict_types=1);

namespace App\Controllers\Category;

use App\Services\AuthService;
use Booking\Exception\BadRequestException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use MeekroDBException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class DeleteCategoryController extends BaseCategoryController
{
    /**
     * @throws UnauthorizedException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $userId = AuthService::getUserIdFromToken($req);
        $data = $this->parseRequestDataToArray($req);

        $data['category_id'] = $id;
        $data['deleted_by'] = $userId;
        $data['updated_by'] = $userId;
        $this->category->delete($data, $data['isRestore'] ?? false);

        return $this->sendJson(null, StatusCode::STATUS_OK, 'Category deleted successfully.');
    }
}
