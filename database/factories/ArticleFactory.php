<?php

namespace Database\Factories;

use App\Models\Article;
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
        $post_type = $this->faker->randomElement(config('post_types'));
        $contents = [
            'addon-introduction' => [
                'description' => $this->faker->realText,
                'author' => $this->faker->name,
                'link' => $this->faker->url,
            ],
            'addon-post' => [
                'description' => $this->faker->realText,
                'author' => $this->faker->name,
            ],
            'page' => [
                'sections' => [
                    ['type' => 'text', 'text' => $this->faker->realText],
                ],
            ],
            'markdown' => [
                'markdown' => $this->faker->realText,
            ],
        ];
        $sentence = $this->faker->sentence;

        return [
            'user_id' => User::factory()->create()->id,
            'title' => $sentence,
            'slug' => $sentence,
            'contents' => $contents[$post_type],
            'post_type' => $post_type,
            'status' => $this->faker->randomElement(config('status')),
        ];
    }
}
