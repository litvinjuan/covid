<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Store\Models\Product;
use Store\Models\Supplier;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'supplier_id' => factory(Supplier::class),
        'title' => $faker->sentence(4),
        'sku' => $faker->randomLetter . " " . $faker->randomNumber(3, true),
        'price' => $faker->numberBetween(100, 100000),
        'stock' => $faker->numberBetween(0, 100),
    ];
});
