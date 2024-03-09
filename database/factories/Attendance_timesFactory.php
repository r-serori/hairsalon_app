<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance_times>
 */
class Attendance_timesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $attendanceIds = [1, 2]; // 使用可能な勤怠IDを定義します
        
        return [
            'attendance_id' => $this->faker->randomElement($attendanceIds),
            'date' => $this->faker->date(),
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'break_time' => $this->faker->numberBetween(1, 60),
        ];
    }
}
