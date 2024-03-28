<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
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
    public function definition()
    {
        $postType = $this->faker->randomElement(array_column(ArticlePostType::cases(), 'value'));
        $contents = [
            ArticlePostType::AddonIntroduction->value => [
                'description' => $this->faker->realText(),
                'author' => $this->faker->name(),
                'link' => $this->faker->url(),
            ],
            ArticlePostType::AddonPost->value => [
                'description' => $this->faker->realText(),
                'author' => $this->faker->name(),
            ],
            ArticlePostType::Page->value => [
                'sections' => [
                    ['type' => 'text', 'text' => $this->faker->realText()],
                ],
            ],
            ArticlePostType::Markdown->value => [
                'markdown' => $this->faker->realText(),
            ],
        ];
        $sentence = $this->faker->sentence();

        return [
            'user_id' => User::factory()->create()->id,
            'title' => $sentence,
            'slug' => $sentence,
            'contents' => $contents[$postType],
            'post_type' => $postType,
            'status' => $this->faker->randomElement(array_column(ArticleStatus::cases(), 'value')),
            'published_at' => now(),
            'modified_at' => now(),
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
                'description' => $this->faker->realText(),
                'license' => $this->faker->realText(),
                'thanks' => $this->faker->realText(),
                'author' => $this->faker->name(),
                'file' => $attachment?->id,
            ],
        ]);
    }

    public function addonIntroduction()
    {
        return $this->state(fn (array $attributes): array => [
            'post_type' => ArticlePostType::AddonIntroduction,
            'contents' => [
                'description' => $this->faker->realText(),
                'license' => $this->faker->realText(),
                'thanks' => $this->faker->realText(),
                'author' => $this->faker->name(),
                'link' => $this->faker->url(),
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
                    ['type' => 'text', 'text' => $this->faker->realText()],
                ],
            ],
        ]);
    }

    public function markdown()
    {
        return $this->state(fn (array $attributes): array => [
            'post_type' => ArticlePostType::Markdown,
            'contents' => [
                'markdown' => $this->faker->realText(),
            ],
        ]);
    }
}
