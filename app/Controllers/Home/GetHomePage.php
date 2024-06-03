<?php

declare(strict_types=1);

namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetHomePage extends Controller
{
    /**
     * Get Home Page Api
     *
     * @param ServerRequestInterface $req
     * @param UserRepository $userRepository
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, UserRepository $userRepository): ResponseInterface
    {
        $res = [
            'message' => 'Welcome to the Home API',
            'data' => [
                'page' => $this->getCurrentPage($req),
                'size' => $this->getPageSize($req),
                'users' => collect($userRepository->getAll())->map(function ($user) {
                    return collect($user)->only(['id', 'name', 'email', 'created_at', 'updated_at']);
                })
            ]
        ];

        return $this->json($res);
    }
}
