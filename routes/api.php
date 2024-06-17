<?php

if (!isset($router)) return;

$router->get('/', App\Controllers\Home\GetHomePage::class);

$router->group('/api/v1/', function ($router) {
    $auth = App\Middlewares\Jwt::class;

    $router->get('/', App\Controllers\Home\GetHomePage::class);

    $router->group('/auth', function ($router) use ($auth) {
        $router->post('/register', App\Controllers\Auth\RegisterAuthController::class);
        $router->post('/login', App\Controllers\Auth\LoginAuthController::class);
        $router->post('/forgot-password', App\Controllers\Auth\ForgotPasswordAuthController::class);
        $router->post('/change-password', App\Controllers\Auth\ChangePasswordAuthController::class);
        $router->post('/activation', App\Controllers\Auth\ActivationAuthController::class);
        $router->get('/me', App\Controllers\Auth\MeAuthController::class, [$auth]);
    });

    $router->group('/contact', function ($router) use ($auth) {
        $router->get('/', App\Controllers\Contact\AllContactController::class, [$auth]);
        $router->get('/{id}', App\Controllers\Contact\DetailContactController::class, [$auth]);
        $router->post('/', App\Controllers\Contact\CreateContactController::class);
    });

    $router->group('/category', function ($router) use ($auth) {
        $router->get('/', App\Controllers\Category\AllCategoryController::class, [$auth]);
        $router->post('/', App\Controllers\Category\CreateCategoryController::class, [$auth]);
        $router->get('/{id}', App\Controllers\Category\DetailCategoryController::class, [$auth]);
        $router->put('/{id}', App\Controllers\Category\UpdateCategoryController::class, [$auth]);
        $router->delete('/{id}', App\Controllers\Category\DeleteCategoryController::class, [$auth]);
     });
});
