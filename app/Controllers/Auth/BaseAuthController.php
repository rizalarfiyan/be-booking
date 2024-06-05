<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Services\AuthService;

class BaseAuthController extends Controller
{
    /** @var AuthService */
    protected AuthService $auth;

    /**
     * Inject the service in the base controller.
     *
     * @param AuthService $auth
     */
    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }
}
