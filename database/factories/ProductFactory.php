<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array {
        return ['name' => fake()->words(3, true), 'code' => fake()->unique()->bothify('SKU-####'), 'price' => fake()->randomFloat(2, 10, 5000), 'tax_percentage' => fake()->randomElement([0, 5, 12, 18]), 'stock_on_hand' => fake()->numberBetween(0, 50)];
    }
}
