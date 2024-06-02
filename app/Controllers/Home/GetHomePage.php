<?php

declare(strict_types=1);

namespace App\Controllers\Home;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetHomePage extends Controller
{
    /**
     * Get Home Page Api
     *
     * @param ServerRequestInterface $req
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $req): ResponseInterface
    {
        $res = [
            'message' => 'Welcome to the Home API',
            'data' => [
                'page' => $this->getCurrentPage($req),
                'size' => $this->getPageSize($req),
            ]
        ];

        return $this->json($res);
    }
}
