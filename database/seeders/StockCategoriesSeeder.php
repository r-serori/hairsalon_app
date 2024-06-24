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
        stock_categories::create([
            'category' => '化粧品', 'owner_id' => 1,
            'created_at' => now(), // Add this line
            'updated_at' => now(),

        ]);
        stock_categories::create([
            'category' => '頭髪系', 'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        stock_categories::create([
            'category' => '整髪料', 'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        stock_categories::create([
            'category' => 'カラー材', 'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        stock_categories::create([
            'category' => 'パーマ材', 'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        stock_categories::create([
            'category' => '顧客飲料', 'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
