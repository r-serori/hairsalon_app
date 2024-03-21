<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     
        DB::table('attendances')->insert([
            'attendance_name' => '田中店長',
            'position' => 'オーナー',
            'phone_number' => '09012345678',
            'address' => '東京都渋谷区',
            'created_at' => now(),
        ]);
        DB::table('attendances')->insert([
            'attendance_name' => '太朗社員',
            'position' => '社員',
            'phone_number' => '09012345678',
            'address' => '東京都渋谷区',
            'created_at' => now(),
        ]);
    }
}
