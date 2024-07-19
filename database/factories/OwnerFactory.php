<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Owner>
 */
class OwnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'store_name' => $this->faker->company(1, 100),
            'postal_code' => $this->faker->postcode(1, 100),
            'prefecture' => $this->faker->word(1, 100),
            'city' => $this->faker->city(1, 100),
            'addressLine1' => $this->faker->streetAddress(1, 200),
            'addressLine2' => $this->faker->secondaryAddress(1, 200),
            'phone_number' => $this->faker->unique()->phoneNumber(1, 20),
            'user_id' =>    1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
