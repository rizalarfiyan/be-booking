<?php

if (!isset($router)) return;

$router->get('/', App\Controllers\Home\GetHomePage::class);

$router->group('/api/v1/', function ($router) {
    $router->get('/', App\Controllers\Home\GetHomePage::class);

    $router->group('/auth', function ($router) {
        $auth = App\Middlewares\Jwt::class;
        $router->post('/register', App\Controllers\Auth\PostRegister::class);
        $router->post('/login', App\Controllers\Auth\PostLogin::class);
        $router->post('/activation', App\Controllers\Auth\PostActivation::class);
        $router->get('/me', App\Controllers\Auth\GetMe::class, [$auth]);
    });
});
