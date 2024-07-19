<?php

namespace Database\Factories;

use App\Models\Owner;
use App\Models\StockCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'product_name' => $this->faker->word(1, 100),
            'product_price' => $this->faker->numberBetween(100, 10000),
            'quantity' => $this->faker->numberBetween(1, 1000),
            'remarks' => $this->faker->sentence(1, 150),
            'supplier' => $this->faker->name(1, 100),
            "notice" => $this->faker->numberBetween(1, 1000),
            'stock_category_id' => StockCategory::factory(),
            'owner_id' => 1,
            'created_at' => now(),
            "updated_at" => now(),
        ];
    }
}
