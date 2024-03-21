<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonthlySalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('monthly_sales')->insert([
            'year' => 2024,
            'month' => 3,
            'monthly_sales' => 211000,
            'created_at' => now(),
        ]);

        DB::table('monthly_sales')->insert([
            'year' => 2024,
            'month' => 4,
            'monthly_sales' => 250000,
            'created_at' => now(),
        ]);

        DB::table('monthly_sales')->insert([
            'year' => 2024,
            'month' => 5,
            'monthly_sales' => 300000,
            'created_at' => now(),
        ]);

        DB::table('monthly_sales')->insert([
            'year' => 2024,
            'month' => 6,
            'monthly_sales' => 350000,
            'created_at' => now(),
        ]);
    }
}
