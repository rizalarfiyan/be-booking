<?php

return [
    'secret' => getenv('JWT_SECRET') ?: randomStr(32),
    'jti'    => getenv('JWT_JTI') ?: 'jwt_booking_auth',
    'ttl'    => (int) getenv('JWT_TTL') ?: (60 * 60 * 24),
];
