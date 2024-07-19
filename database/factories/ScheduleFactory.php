<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // 出勤時間を生成
        $startTime = $this->faker->dateTimeThisMonth();

        // 出勤時間の1時間後から、同じ日の範囲内で退勤時間を生成
        $endTime = $this->faker->dateTimeBetween(
            $startTime, // 出勤時間以降
            Carbon::parse($startTime->format('Y-m-d') . ' 23:59:59') // 同じ日の23:59:59まで
        );

        // 出勤時間より遅い退勤時間を設定（ただし、同じ日に限定）
        if ($endTime <= $startTime) {
            Carbon::parse($startTime->format('Y-m-d') . ' 23:59:59'); // 同じ日の23:59:59まで
        }

        // 出勤時間と退勤時間が同じ日にあることを確認
        if ($endTime->format('Y-m-d') !== $startTime->format('Y-m-d')) {
            Carbon::parse($startTime->format('Y-m-d') . ' 23:59:59'); // 同じ日の23:59:59まで
        }

        return [
            'title' =>  '',
            'start_time' =>  $startTime->format('Y-m-d H:i:s'),
            'end_time' =>  $endTime->format('Y-m-d H:i:s'),
            'allDay' => 0,
            'customer_id' => Customer::inRandomOrder()->first()->id,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
