<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Article;
use Illuminate\Support\Str;
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

    $contents = [
        'description' => $faker->realText,
        'author' => $faker->name,
    ];
    return [
        'title' => $faker->sentence,
        'contents' => $contents,
        'status' => $faker->randomElement(config('status')),
    ];
});
