<?php

if (!isset($router)) return;

$router->get('/', App\Controllers\Home\GetHomePage::class);

$router->group('/api/v1/', function ($router) {
    $auth = App\Middlewares\Jwt::class;

    $router->get('/', App\Controllers\Home\GetHomePage::class);

    $router->group('/auth', function ($router) use ($auth) {
        $router->post('/register', App\Controllers\Auth\PostRegister::class);
        $router->post('/login', App\Controllers\Auth\PostLogin::class);
        $router->post('/forgot-password', App\Controllers\Auth\PostForgotPassword::class);
        $router->post('/change-password', App\Controllers\Auth\PostChangePassword::class);
        $router->post('/activation', App\Controllers\Auth\PostActivation::class);
        $router->get('/me', App\Controllers\Auth\GetMe::class, [$auth]);
    });

    $router->group('/contact', function ($router) use ($auth) {
        $router->get('/', App\Controllers\Contact\GetAll::class, [$auth]);
        $router->get('/{id}', App\Controllers\Contact\GetDetail::class, [$auth]);
        $router->post('/', App\Controllers\Contact\PostNewContact::class);
    });
});
