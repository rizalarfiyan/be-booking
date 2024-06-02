<?php

use Psr\Http\Message\ServerRequestInterface;
use Booking\Response\Response;
use Booking\Emitter\SapiEmitter;
use Booking\Request\ServerRequestFactory;

return [
    'emitter'  => DI\create(SapiEmitter::class),
    'response' => DI\create(Response::class),
    'request'  => function () {
        return ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
    },
    ServerRequestInterface::class   => DI\get('request'),
];
