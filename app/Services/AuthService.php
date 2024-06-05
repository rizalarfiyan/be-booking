<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\UserRepository;

class AuthService
{
    /** @var UserRepository */
    protected UserRepository $user;

    /**
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function register($data): void
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, [
            'cost' => 12,
        ]);

        $this->user->insert($data);
    }
}
