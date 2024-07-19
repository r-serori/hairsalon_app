<?php

namespace Database\Factories;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailySale>
 */
class DailySaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date' => $this->faker->date(),
            'daily_sales' => $this->faker->randomFloat(2, 0, 200000),
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
