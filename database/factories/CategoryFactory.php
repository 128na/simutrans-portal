<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'type' => $this->faker->randomElement(CategoryType::cases()),
            'slug' => 'dummy-'.$this->faker->randomNumber(8),
            'order' => $this->faker->randomNumber(1),
            'need_admin' => false,
        ];
    }
}
