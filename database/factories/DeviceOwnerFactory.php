<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\DeviceOwner;
use Faker\Generator as Faker;

$factory->define(DeviceOwner::class, static function (Faker $faker) {

    return [
        'title' => $faker->company,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s')
    ];
});
