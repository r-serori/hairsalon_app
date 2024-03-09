<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stocks>
 */
class StocksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'product_name' => $this->faker->name(),
            'category' => $this->faker->word(),
            'quantity' => $this->faker->numberBetween(1, 100),
            'remarks' => $this->faker->sentence(),
            'purchase_price' => $this->faker->numberBetween(100, 10000),
            'supplier' => $this->faker->company(),


        ];
    }
}
