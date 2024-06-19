<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use App\Services\AuthService;
use Booking\Exception\BadRequestException;
use Booking\Exception\UnauthorizedException;
use Booking\Exception\UnprocessableEntitiesException;
use Booking\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class CreateBookController extends BaseBookController
{
    /**
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     * @throws UnauthorizedException
     * @throws BadRequestException
     * @throws UnprocessableEntitiesException
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $id = AuthService::getUserIdFromToken($req);
        $data = $req->getParsedBody();

        $validation = v::key('isbn', v::stringType()->length(5, 50))
            ->key('sku', v::stringType()->length(5, 50))
            ->key('author', v::arrayVal()->each(v::stringType()), false)
            ->key('categoryId', v::arrayVal()->each(v::intVal()))
            ->key('title', v::stringType()->length(5, 120))
            ->key('slug', v::stringType()->length(5, 120))
            ->key('pages', v::intVal())
            ->key('weight', v::floatVal())
            ->key('height', v::floatVal())
            ->key('width', v::floatVal())
            ->key('language', v::stringType()->length(2, 20))
            ->key('year', v::intVal()->between(1900, date('Y')))
            ->key('publishedAt', v::date())
            ->key('description', v::stringType());

        $validation->assert($data);

        $file = $req->getUploadedFiles()['image'];
        $ext = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $filename = sprintf('images/%s.%s', randomStr(64), $ext);
        $file->moveTo($filename);

        $data['image'] = $filename;
        $data['author'] = collect($data['author'])->map(fn($author) => trim($author))->unique()->toArray();
        $data['categoryId'] = collect($data['categoryId'])->map(fn($author) => (int)trim($author))->unique()->toArray();
        $data['createdBy'] = $id;
        $data['updatedBy'] = $id;

        $this->book->create($data);
        return $this->sendJson(null, StatusCode::STATUS_CREATED, 'Book created successfully.');
    }
}
