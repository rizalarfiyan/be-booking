<?php

declare(strict_types=1);

namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Repository\UserRepository;
use App\Services\AuthService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetHomePage extends Controller
{
    /**
     * Get Home Page Api.
     *
     * @param ServerRequestInterface $req
     * @param UserRepository $userRepository
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req, UserRepository $userRepository): ResponseInterface
    {
        $res = [
            'message' => 'Welcome to the Home API',
            'data' => [],
        ];

        return $this->json($res);
    }
}
