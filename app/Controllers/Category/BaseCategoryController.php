<?php

declare(strict_types=1);

namespace App\Controllers\Category;

use App\Controllers\Controller;
use App\Services\CategoryService;

class BaseCategoryController extends Controller
{
    /** @var CategoryService */
    protected CategoryService $category;

    /**
     * Inject the service in the base controller.
     *
     * @param CategoryService $category
     */
    public function __construct(CategoryService $category)
    {
        $this->category = $category;
    }
}
