<?php

if (!isset($router)) return;

$router->get('/', App\Controllers\Home\GetHomePage::class);

$router->group('/api/v1/', function ($router) {
    $router->get('/', App\Controllers\Home\GetHomePage::class);

    $router->group('/auth', function ($router) {
        $auth = App\Middlewares\Jwt::class;
        $router->post('/register', App\Controllers\Auth\PostRegister::class);
        $router->post('/login', App\Controllers\Auth\PostLogin::class);
        $router->post('/forgot-password', App\Controllers\Auth\PostForgotPassword::class);
        $router->post('/change-password', App\Controllers\Auth\PostChangePassword::class);
        $router->post('/activation', App\Controllers\Auth\PostActivation::class);
        $router->get('/me', App\Controllers\Auth\GetMe::class, [$auth]);
    });

    $router->group('/contact', function ($router) {
        $router->post('/', App\Controllers\Contact\PostNewContact::class);
    });

    $router->group('/category', function ($router) {
        $auth = App\Middlewares\Jwt::class;
        // get by id using url path
        $router->get('/{id}', App\Controllers\Category\GetCategoriesById::class, [$auth]);
        $router->post('/', App\Controllers\Category\PostNewCategoryController::class, [$auth]);
        $router->put('/{id}', App\Controllers\Category\UpdateCategoryController::class, [$auth]);
        $router->delete('/{id}', App\Controllers\Category\DeleteCategoryController::class, [$auth]);
     });
});
