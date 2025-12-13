<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    #[\Override]
    public function definition()
    {
        return [
            'role' => UserRole::User,
            'name' => fake()->name(),
            'nickname' => 'dummy_'.fake()->randomNumber(8),
            'invited_by' => null,
            'invitation_code' => Str::random(10),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'two_factor_confirmed_at' => null,
            'two_factor_secret' => null,
            'remember_token' => Str::random(10),
            'two_factor_recovery_codes' => null,
        ];
    }

    public function admin()
    {
        return $this->state(fn (array $attributes): array => [
            'role' => UserRole::Admin,
        ]);
    }

    public function deleted()
    {
        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
        ]);
    }
}
