<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class Attendance_timesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('attendance_times')->insert([
            'date' => '2021-01-01', // Add this line
            'attendance_id' => 1,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'break_time' => '60', // Add this line
            'created_at' => now(),
        ]);
        DB::table('attendance_times')->insert([
            'date' => '2022-01-01', // Add this line
            'attendance_id' => 2,
            'start_time' => '23:00:00',
            'end_time' => '24:00:00',
            'break_time' => '60', // Add this line
            'created_at' => now(),
        ]);

    }
}
