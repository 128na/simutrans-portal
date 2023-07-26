<?php

declare(strict_types=1);

namespace Database\Factories\User;

use App\Models\User\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => 'Hello!',
            'website' => 'http://example.com',
        ];
    }
}
