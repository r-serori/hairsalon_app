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
            'name' => '田中店長',
            'position' => 'オーナー',
            'phone_number' => '090-1234-5678',
            'address' => '東京都渋谷区',
            'created_at' => now(),
        ]);
        DB::table('attendances')->insert([
            'name' => '太朗社員',
            'position' => '社員',
            'phone_number' => '090-1234-5678',
            'address' => '東京都渋谷区',
            'created_at' => now(),
        ]);
    }
}
