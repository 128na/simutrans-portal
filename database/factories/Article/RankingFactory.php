<?php

declare(strict_types=1);

namespace Database\Factories\Article;

use App\Models\Article;
use App\Models\Article\Ranking;
use Illuminate\Database\Eloquent\Factories\Factory;

final class RankingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ranking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[\Override]
    public function definition()
    {
        return [
            'rank' => $this->faker->randomNumber(1),
            'article_id' => Article::factory()->publish()->create()->id,
        ];
    }
}
