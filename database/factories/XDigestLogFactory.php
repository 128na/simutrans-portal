<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\XDigestLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<XDigestLog>
 */
class XDigestLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<XDigestLog>
     */
    protected $model = XDigestLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function definition(): array
    {
        return [
            'cutoff_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'article_count' => fake()->numberBetween(0, 3),
        ];
    }
}
