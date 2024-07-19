<?php

namespace Database\Seeders;

use App\Models\YearlySale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class YearlySaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('yearly_sales')->insert([
        //     'year' => 2024,
        //     'yearly_sales' => 1000000,
        //     'owner_id' => 1,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // DB::table('yearly_sales')->insert([
        //     'year' => 2023,
        //     'yearly_sales' => 1200000,
        //     'owner_id' => 1,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // DB::table('yearly_sales')->insert([
        //     'year' => 2022,
        //     'yearly_sales' => 1500000,
        //     'owner_id' => 1,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // DB::table('yearly_sales')->insert([
        //     'year' => 2021,
        //     'yearly_sales' => 1800000,
        //     'owner_id' => 1,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
        YearlySale::factory()
            ->count(8)
            ->create();
    }
}
