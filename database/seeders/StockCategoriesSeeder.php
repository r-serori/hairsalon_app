<?php

namespace Database\Seeders;

use App\Models\StockCategory;

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
        StockCategory::create([
            'category' => '化粧品',
            'owner_id' => 1,
            'created_at' => now(), // Add this line
            'updated_at' => now(),

        ]);
        StockCategory::create([
            'category' => '頭髪系',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        StockCategory::create([
            'category' => '整髪料',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        StockCategory::create([
            'category' => 'カラー材',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        StockCategory::create([
            'category' => 'パーマ材',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        StockCategory::create([
            'category' => '顧客飲料',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
