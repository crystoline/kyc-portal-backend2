<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Lga;
use Faker\Generator as Faker;

$factory->define(Lga::class, function (Faker $faker) {

    return [
        'state_id' => $faker->word,
        'name' => $faker->word,
        'code' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
