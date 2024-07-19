<?php

namespace Database\Seeders;

use App\Models\StockCategory;

use Illuminate\Database\Seeder;

class StockCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StockCategory::factory()
            ->count(10)
            ->create();
    }
}
