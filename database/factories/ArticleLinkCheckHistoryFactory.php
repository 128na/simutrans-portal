<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleLinkCheckHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ArticleLinkCheckHistory>
 */
class ArticleLinkCheckHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ArticleLinkCheckHistory>
     */
    protected $model = ArticleLinkCheckHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function definition(): array
    {
        return [
            'article_id' => Article::factory(),
            'failed_count' => fake()->numberBetween(0, 10),
            'last_checked_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
