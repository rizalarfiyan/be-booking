<?php

declare(strict_types=1);

namespace App;

interface Constants
{
    public const TYPE_VERIFICATION_ACTIVATION = 'activation';

    public const TYPE_VERIFICATION_FORGOT_PASSWORD = 'forgot_password';

    public const TYPE_USER_ACTIVE = 'active';

    public const TYPE_USER_INACTIVE = 'inactive';
}
