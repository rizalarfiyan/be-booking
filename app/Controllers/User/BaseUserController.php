<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\Controller;
use App\Services\UserService;

class BaseUserController extends Controller
{
    /**
     * @var UserService
     */
    protected UserService $user;

    /**
     * @param UserService $user
     */
    public function __construct(UserService $user)
    {
        $this->user = $user;
    }
}
