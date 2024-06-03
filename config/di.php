<?php

use Booking\Emitter\SapiEmitter;
use Booking\Request\ServerRequestFactory;
use Booking\Response\Response;
use Psr\Http\Message\ServerRequestInterface;

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
