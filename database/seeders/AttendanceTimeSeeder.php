<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AttendanceTime;
use Carbon\Carbon;

class AttendanceTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // AttendanceTime::factory()
        //     ->count(10)
        //     ->create();
        $yesterdayStart = Carbon::yesterday('Asia/Tokyo')->setTime(15, 30)->format('Y-m-d H:i:s');

        AttendanceTime::create([
            'start_time' => $yesterdayStart,
            'end_time' => null,
            'start_photo_path' => 'https://example.com/attendance/1/start',
            'end_photo_path' => null,
            'user_id' => 3,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
