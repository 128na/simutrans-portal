<?php

declare(strict_types=1);

namespace Database\Factories\User;

use App\Models\User;
use App\Models\User\LoginHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

final class LoginHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoginHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[\Override]
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'ip' => '100.0.0.1',
            'ua' => 'foo',
            'referer' => 'http://example.com',
        ];
    }
}
