<?php

namespace Database\Factories\Article;

use App\Models\Article;
use App\Models\Article\TweetLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class TweetLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TweetLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->randomNumber(),
            'article_id' => Article::factory()->create()->id,
            'text' => $this->faker->text(),
            'retweet_count' => $this->faker->randomNumber(),
            'reply_count' => $this->faker->randomNumber(),
            'like_count' => $this->faker->randomNumber(),
            'quote_count' => $this->faker->randomNumber(),
            'tweet_created_at' => Carbon::parse($this->faker->date()),
        ];
    }
}
