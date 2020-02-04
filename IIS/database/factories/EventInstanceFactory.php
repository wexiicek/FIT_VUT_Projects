<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
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

$factory->define(App\EventInstance::class, function (Faker $faker) {
    $times = ["00", "10", "20", "30", "40", "50"];
    return [
        'date' => date("d. m. Y", strtotime(now())),
        'time' => strval(rand(0, 23)).":".$times[rand(0,5)],
        'price' => rand(5,100),
        'room_id' => rand(1,15),
        'event_id' => rand(1,10),
    ];
});
