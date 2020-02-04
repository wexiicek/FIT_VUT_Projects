<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'firstname' => Str::random(10),
        'lastname' => Str::random(10),
        'username' => Str::random(10),
        'phoneNumber' => rand(10000000,999999999),
        'email' => Str::random(10).'@gmail.com',
        'password' => bcrypt('password'),
    ];
});
