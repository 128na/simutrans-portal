<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MyList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MyList>
 */
class MyListFactory extends Factory
{
    protected $model = MyList::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'note' => fake()->boolean(40) ? fake()->realText(50) : null,
            'is_public' => false,
            'slug' => null,
        ];
    }

    public function public(): self
    {
        return $this->state(fn() => [
            'is_public' => true,
            'slug' => fake()->slug(),
        ]);
    }
}
