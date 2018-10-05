<?php

use Faker\Generator as Faker;

$factory->define(\App\Advert::class, function (Faker $faker) {
    return [
        'title' => $faker->text(40),
        'engine' => $faker->text(10),
        'torque' => $faker->text(15),
        'make' => $faker->text(10),
        'model' => $faker->text(20),
        'exterior_color' => $faker->text(20),
        'interior_color' => $faker->text(20),
        'transmission' => $faker->text(20),
        'type_of_drive' => $faker->text(20),
        'condition' => $faker->text(20),
        'body_style' => $faker->text(20),
        'mileage' => random_int(0, 100000),
        'price' => random_int(0, 100000),
        'year' => random_int(2000, 2018),
        'number_of_seats' => random_int(2, 6),
        'doors' => random_int(2, 6),
        'weight' => random_int(500, 3500),
        'description' => $faker->text(300),
        'contact_number' => $faker->randomFloat(0,900000000, 10000000000000),
        'email' => $faker->text(20),
        'main_image' => $faker->text(20),
    ];
});
