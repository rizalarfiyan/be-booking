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
     * @throws MeekroDBException
     */
    public function __invoke(int $id, ServerRequestInterface $req): ResponseInterface
    {
        $user_id = AuthService::getUserIdFromToken($req);

        $data['category_id'] = $id;
        $data['deleted_by'] = $user_id;
        $this->category->delete($data);

        return $this->sendJson(null, StatusCode::STATUS_OK, 'Category deleted successfully.');
    }
}
