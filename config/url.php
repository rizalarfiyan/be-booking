<?php

return [
    'activation'        => getenv('FE_ACTIVATION_URL') ?: 'http://localhost:3000/activation',
    'change_password'   => getenv('FE_CHANGE_PASSWORD_URL') ?: 'http://localhost:3000/change-password',
];
