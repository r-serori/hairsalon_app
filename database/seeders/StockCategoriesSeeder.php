<?php

namespace Database\Seeders;

use App\Models\stock_categories;



use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\stock_categories::create(['category' => '化粧品']);
        \App\Models\stock_categories::create(['category' => '頭髪系']);
        \App\Models\stock_categories::create(['category' => '整髪料']);
        \App\Models\stock_categories::create(['category' => 'カラー材']);
        \App\Models\stock_categories::create(['category' => 'パーマ材']);
        \App\Models\stock_categories::create(['category' => '顧客飲料']);
    }
}
