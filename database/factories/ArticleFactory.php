<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
final class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    #[\Override]
    public function definition()
    {
        $postType = fake()->randomElement(array_column(ArticlePostType::cases(), 'value'));
        $contents = [
            ArticlePostType::AddonIntroduction->value => [
                'description' => fake()->realText(),
                'author' => fake()->name(),
                'link' => fake()->url(),
            ],
            ArticlePostType::AddonPost->value => [
                'description' => fake()->realText(),
                'author' => fake()->name(),
            ],
            ArticlePostType::Page->value => [
                'sections' => [
                    ['type' => 'text', 'text' => fake()->realText()],
                ],
            ],
            ArticlePostType::Markdown->value => [
                'markdown' => fake()->realText(),
            ],
        ];
        $sentence = fake()->sentence();

        return [
            'user_id' => User::factory()->create()->id,
            'title' => $sentence,
            'slug' => $sentence,
            'contents' => $contents[$postType],
            'post_type' => $postType,
            'status' => fake()->randomElement(array_column(ArticleStatus::cases(), 'value')),
            'published_at' => now()->subDays(2),
            'modified_at' => now()->subDays(1),
        ];
    }

    public function publish()
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ArticleStatus::Publish,
        ]);
    }

    public function draft()
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ArticleStatus::Draft,
        ]);
    }

    public function deleted()
    {
        return $this->state(fn (array $attributes): array => [
            'deleted_at' => now(),
        ]);
    }

    public function addonPost(?Attachment $attachment = null)
    {
        return $this->state(fn (array $attributes): array => [
            'post_type' => ArticlePostType::AddonPost,
            'contents' => [
                'description' => fake()->realText(),
                'license' => fake()->realText(),
                'thanks' => fake()->realText(),
                'author' => fake()->name(),
                'file' => $attachment?->id,
            ],
        ]);
    }

    public function addonIntroduction()
    {
        return $this->state(fn (array $attributes): array => [
            'post_type' => ArticlePostType::AddonIntroduction,
            'contents' => [
                'description' => fake()->realText(),
                'license' => fake()->realText(),
                'thanks' => fake()->realText(),
                'author' => fake()->name(),
                'link' => fake()->url(),
                'agreement' => true,
                'exclude_link_check' => false,
            ],
        ]);
    }

    public function page()
    {
        return $this->state(fn (array $attributes): array => [
            'post_type' => ArticlePostType::Page,
            'contents' => [
                'sections' => [
                    ['type' => 'text', 'text' => fake()->realText()],
                ],
            ],
        ]);
    }

    public function markdown()
    {
        return $this->state(fn (array $attributes): array => [
            'post_type' => ArticlePostType::Markdown,
            'contents' => [
                'markdown' => fake()->realText(),
            ],
        ]);
    }
}
