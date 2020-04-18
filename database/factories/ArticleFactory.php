<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Article;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
 */

$factory->define(Article::class, function (Faker $faker) {

    $post_type = $faker->randomElement(config('post_types'));
    $contents = [
        'addon-introduction' => [
            'description' => $faker->realText,
            'author' => $faker->name,
            'link' => $faker->url,
        ],
        'addon-post' => [
            'description' => $faker->realText,
            'author' => $faker->name,
        ],
        'page' => [
            'sections' => [
                ['type' => 'text', 'text' => $faker->realText],
            ],
        ],
        'markdown' => [
            'markdown' => $faker->realText,
        ],
    ];
    $sentence = $faker->sentence;
    return [
        'title' => $sentence,
        'slug' => $sentence,
        'contents' => $contents[$post_type],
        'post_type' => $post_type,
        'status' => $faker->randomElement(config('status')),
    ];
});
