<?php

namespace Database\Factories;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\YearlySale>
 */
class YearlySaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'year' => $this->faker->year(),
            'yearly_sales' => $this->faker->randomFloat(2, 0, 999999999),
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
