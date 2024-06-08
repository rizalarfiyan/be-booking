<?php

return [
    'host' => getenv('MAIL_HOST') ?? '127.0.0.1',
    'username' => getenv('MAIL_USERNAME') ?? '',
    'password' => getenv('MAIL_PASSWORD') ?? '',
    'port' => getenv('MAIL_PORT') ?? 1025,
    'email' => getenv('MAIL_FROM_EMAIL') ?? 'noreply@booking.com',
    'name' => getenv('MAIL_FROM_NAME') ?? 'Booking',
];
