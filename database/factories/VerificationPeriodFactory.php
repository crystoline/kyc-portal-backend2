<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\VerificationPeriod;
use Faker\Generator as Faker;

$factory->define(VerificationPeriod::class, function (Faker $faker) {

    return [
        'title' => $faker->word,
        'date_start' => $faker->date('Y-m-d H:i:s'),
        'territory_id' => $faker->word,
        'state_id' => $faker->word,
        'lga_id' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
