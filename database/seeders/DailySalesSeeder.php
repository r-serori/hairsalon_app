<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DailySalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('daily_sales')->insert([
            'date' => '2024-03-12',
            'daily_sales' => 22000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('daily_sales')->insert([
            'date' => '2024-03-13',
            'daily_sales' => 38000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('daily_sales')->insert([
            'date' => '2024-03-14',
            'daily_sales' => 46000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('daily_sales')->insert([
            'date' => '2024-03-15',
            'daily_sales' => 50000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('daily_sales')->insert([
            'date' => '2024-08-16',
            'daily_sales' => 55000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('daily_sales')->insert([
            'date' => '2024-08-17',
            'daily_sales' => 60000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('daily_sales')->insert([
            'date' => '2024-08-18',
            'daily_sales' => 65000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('daily_sales')->insert([
            'date' => '2024-08-19',
            'daily_sales' => 70000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('daily_sales')->insert([
            'date' => '2024-08-20',
            'daily_sales' => 75000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('daily_sales')->insert([
            'date' => '2024-08-21',
            'daily_sales' => 80000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('daily_sales')->insert([
            'date' => '2024-08-22',
            'daily_sales' => 85000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
