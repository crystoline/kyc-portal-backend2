<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Verification;
use Faker\Generator as Faker;

$factory->define(Verification::class, function (Faker $faker) {

    return [
        'is_first_registration' => $faker->word,
        'agent_id' => $faker->word,
        'verified_by' => $faker->word,
        'approved_by' => $faker->word,
        'date' => $faker->word,
        'status' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
