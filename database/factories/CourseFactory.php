<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Owner;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [

            'course_name' => $this->faker->word(1, 100),
            'price' => $this->faker->numberBetween(100, 10000),
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
