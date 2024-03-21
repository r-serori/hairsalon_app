<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('schedules')->insert([
            'customer_name' => 'John Doe',
            'date' => '2024-03-12',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'price' => 10000,
            'created_at' => now(),
        ]);

        DB::table('schedules')->insert([
            'customer_name' => 'Jane Doe',
            'date' => '2024-03-12',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'price' => 12000,
            'created_at' => now(),
        ]);

        DB::table('schedules')->insert([
            'customer_name' => 'John Doe',
            'date' => '2024-03-13',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'price' => 20000,
            'created_at' => now(),
        ]);

        DB::table('schedules')->insert([
            'customer_name' => 'Jane Doe',
            'date' => '2024-03-13',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'price' => 18000,
            'created_at' => now(),
        ]);

        DB::table('schedules')->insert([
            'customer_name' => 'John Doe',
            'date' => '2024-03-14',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'price' => 23000,
            'created_at' => now(),
        ]);

        DB::table('schedules')->insert([
            'customer_name' => 'Jane Doe',
            'date' => '2024-03-14',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'price' => 15000,
            'created_at' => now(),
        ]);

        DB::table('schedules')->insert([
            'customer_name' => 'John Doe',
            'date' => '2024-03-15',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'price' => 90000,
            'created_at' => now(),
        ]);
        
    }
}

