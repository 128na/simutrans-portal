<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
final class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[\Override]
    public function definition()
    {
        return [
            'type' => fake()->randomElement(CategoryType::cases()),
            'slug' => 'dummy-'.fake()->randomNumber(8),
            'order' => fake()->randomNumber(1),
            'need_admin' => false,
        ];
    }
}
