<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(User::class, static function (Faker $faker) {

    return [
        'group_id' => [1,2,3,4][random_int(0,3)],
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'gender' => ['male', 'female'][random_int(0,1)],
        'email' => $faker->email,
        'email_verified_at' => $faker->date('Y-m-d H:i:s'),
        'password' => 'password',
    ];
});
