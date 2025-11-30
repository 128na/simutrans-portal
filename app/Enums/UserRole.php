<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    /**
     * 管理者
     */
    case Admin = 'admin';

    /**
     * 一般
     */
    case User = 'user';
}
