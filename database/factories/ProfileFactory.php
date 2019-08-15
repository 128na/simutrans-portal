<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\Profile;
use App\Models\Contents\ProfileData;
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

$factory->define(Profile::class, function (Faker $faker) {

    return [
        'data' => new ProfileData([
                'description' => 'Hello!',
                'twitter'     => 'twitter_jp',
                'website'     => 'http://example.com',
        ]),
    ];
});
