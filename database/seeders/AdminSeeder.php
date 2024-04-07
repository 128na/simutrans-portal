<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 管理者追加.
 */
final class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (is_null(env('ADMIN_EMAIL'))) {
            return;
        }

        User::firstOrCreate(
            ['role' => UserRole::Admin, 'name' => env('ADMIN_NAME'), 'email' => env('ADMIN_EMAIL')],
            ['password' => bcrypt(env('ADMIN_PASSWORD')), 'email_verified_at' => now()],
        );
    }
}
