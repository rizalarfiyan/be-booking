<?php

return [
    'env'      => getenv('APP_ENV') ?: 'production',
    'key'      => getenv('APP_KEY') ?: 'randomkey',
    'url'      => getenv('APP_URL') ?: 'http://localhost',
    'port'     => getenv('APP_PORT') ?: 8080,
    'name'     => getenv('APP_NAME') ?: 'Booking',
    'cache_dir' => getenv('APP_CACHE_DIR') ?: __DIR__.'/../tmp',
    'page_size' => 15,
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
];
