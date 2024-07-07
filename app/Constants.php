<?php

declare(strict_types=1);

namespace App;

interface Constants
{
    public const TYPE_VERIFICATION_ACTIVATION = 'activation';

    public const TYPE_VERIFICATION_FORGOT_PASSWORD = 'forgot_password';

    public const TYPE_USER_ACTIVE = 'active';

    public const TYPE_USER_INACTIVE = 'inactive';

    public const TYPE_USER_BANNED = 'banned';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_READER = 'reader';

    public const LEADERBOARD_LIMIT = 10;

    public const LEADERBOARD_MAX_RANK = 500;
}
