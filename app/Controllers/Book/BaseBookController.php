<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use App\Controllers\Controller;
use App\Services\AuthService;
use App\Services\BookService;
use Booking\Constants;
use Booking\Exception\BadRequestException;
use Booking\Exception\UnauthorizedException;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;

class BaseBookController extends Controller
{
    /** @var BookService */
    protected BookService $book;

    /**
     * Inject the service in the base controller.
     *
     * @param BookService $book
     */
    public function __construct(BookService $book)
    {
        $this->book = $book;
    }

    /**
     * @param ServerRequestInterface $req
     * @param bool $isEdit
     * @return array|object
     * @throws BadRequestException
     * @throws UnauthorizedException
     */
    public function getPayloadCreateOrEdit(ServerRequestInterface $req, bool $isEdit = false): array|object
    {
        $id = AuthService::getUserIdFromToken($req);
        $data = $req->getParsedBody();

        $validation = v::key('isbn', v::stringType()->length(5, 50))
            ->key('sku', v::stringType()->length(5, 50))
            ->key('author', v::arrayVal()->each(v::stringType()), false)
            ->key('category', v::arrayVal()->each(v::intVal()))
            ->key('title', v::stringType()->length(5, 120))
            ->key('slug', v::stringType()->length(5, 120))
            ->key('pages', v::intVal())
            ->key('weight', v::floatVal())
            ->key('height', v::floatVal())
            ->key('width', v::floatVal())
            ->key('language', v::stringType()->length(2, 20))
            ->key('publishedAt', v::date())
            ->key('description', v::stringType());

        $validation->assert($data);

        $file = $req->getUploadedFiles();
        if (! $isEdit || isset($file['image'])) {
            $validation = v::key('image', v::notBlank());
            $validation->assert($file);

            $image = $file['image'];
            if ($image->getError() !== UPLOAD_ERR_OK) {
                throw new BadRequestException(Constants::VALIDATION_MESSAGE, [
                    'image' => 'Invalid image file.',
                ]);
            }

            if (! in_array($image->getClientMediaType(), ['image/jpeg', 'image/jpg', 'image/png'])) {
                throw new BadRequestException(Constants::VALIDATION_MESSAGE, [
                    'image' => 'Invalid image file type.',
                ]);
            }

            if ($image->getSize() > 1024 * 1024 * 2) {
                throw new BadRequestException(Constants::VALIDATION_MESSAGE, [
                    'image' => 'Image file size must be less than 2MB.',
                ]);
            }

            $ext = pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);
            $filename = sprintf('images/%s.%s', randomStr(64), $ext);
            $image->moveTo($filename);
        }

        $data['image'] = $filename ?? null;
        $data['author'] = collect($data['author'])->map(fn ($author) => trim($author))->unique()->toArray();
        $data['category'] = collect($data['category'])->map(fn ($author) => (int) trim($author))->unique()->toArray();
        $data['createdBy'] = $id;
        $data['updatedBy'] = $id;

        return $data;
    }
}
