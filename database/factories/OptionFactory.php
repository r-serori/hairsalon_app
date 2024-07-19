<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class OptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'option_name' => $this->faker->word(1, 100),
            'price' => $this->faker->numberBetween(100, 10000),
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
