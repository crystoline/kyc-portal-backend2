<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Agent;
use Faker\Generator as Faker;

$factory->define(Agent::class, static function (Faker $faker) {

    return [
        'code' => strtoupper(base_convert(random_int(100000000, 999999999), 10, 36)),
        'type' => ['principal-agent', 'sole-agent'][random_int(0,1)],
        'is_app_only' => [0,1][random_int(0,1)],
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'user_name' => $faker->userName,
        'gender' => ['male', 'female'][random_int(0,1)],
        'date_of_birth' => $faker->date(),
        'passport' => $faker->imageUrl(240, 320 ),
    ];
});
