<?php

namespace Database\Factories\User;

use App\Models\User;
use App\Models\User\Bookmark;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookmarkFactory extends Factory
{
    protected $model = Bookmark::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'title' => $this->faker->word,
            'description' => $this->faker->text,
            'is_public' => random_int(0, 1),
        ];
    }
}
