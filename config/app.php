<?php

return [
    'env'      => getenv('APP_ENV') ?: 'production',
    'key'      => getenv('APP_KEY') ?: 'randomkey',
    'url'      => getenv('APP_URL') ?: 'http://localhost',
    'port'     => getenv('APP_PORT') ?: 8080,
    'name'     => getenv('APP_NAME') ?: 'Booking',
];
