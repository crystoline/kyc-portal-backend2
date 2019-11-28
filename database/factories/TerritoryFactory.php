<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Territory;
use Faker\Generator as Faker;

$factory->define(Territory::class, function (Faker $faker) {

    return [
        'name' => $faker->city,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
