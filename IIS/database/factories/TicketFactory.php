<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Room;
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

$factory->define(App\Ticket::class, function (Faker $faker) {
    $user = User::find(rand(1, 15));
    $instance = \App\EventInstance::find(rand(1, 50));
    return [
        'user_id' => $user->id,
        'ticket_amount' => rand(1, 5),
        'price' => rand(100, 5000),
        'email' => $user->email,
        'paid' => rand(0,1) ? true : false,
        'seats' => null,
        'eventInstance_id' => $instance->id,
    ];
});
