<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ScreenshotStatus;
use App\Models\Screenshot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ScreenshotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Screenshot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'links' => $this->faker->url(),
            'status' => ScreenshotStatus::Publish,
        ];
    }
}
