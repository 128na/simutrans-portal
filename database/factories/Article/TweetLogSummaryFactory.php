<?php

namespace Database\Factories\Article;

use App\Models\Article;
use App\Models\Article\TweetLogSummary;
use Illuminate\Database\Eloquent\Factories\Factory;

class TweetLogSummaryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TweetLogSummary::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'article_id' => Article::factory()->create()->id,
            'total_retweet_count' => $this->faker->randomNumber(),
            'total_reply_count' => $this->faker->randomNumber(),
            'total_like_count' => $this->faker->randomNumber(),
            'total_quote_count' => $this->faker->randomNumber(),
        ];
    }
}
