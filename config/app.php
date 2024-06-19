<?php

return [
    'env'      => getenv('APP_ENV') ?: 'production',
    'key'      => getenv('APP_KEY') ?: 'randomkey',
    'url'      => getenv('APP_URL') ?: 'http://localhost:8081/',
    'port'     => getenv('APP_PORT') ?: 8081,
    'name'     => getenv('APP_NAME') ?: 'Booking',
    'cache_dir' => getenv('APP_CACHE_DIR') ?: __DIR__.'/../tmp',
    'count' => 10,
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
];
