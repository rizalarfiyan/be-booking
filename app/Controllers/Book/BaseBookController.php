<?php

declare(strict_types=1);

namespace App\Controllers\Book;

use App\Controllers\Controller;
use App\Services\BookService;

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
}
