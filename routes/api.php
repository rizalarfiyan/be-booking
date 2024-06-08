<?php

if (!isset($router)) return;

$router->get('/', App\Controllers\Home\GetHomePage::class);

$router->group('/api/v1/', function ($router) {
    $router->get('/', App\Controllers\Home\GetHomePage::class);

    $router->group('/auth', function ($router) {
        $router->post('/register', App\Controllers\Auth\PostRegister::class);
        $router->post('/login', App\Controllers\Auth\PostLogin::class);
    });
});
