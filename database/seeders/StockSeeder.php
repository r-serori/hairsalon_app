<?php

namespace Database\Seeders;

use App\Models\Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('stocks')->insert([
            'product_name' => 'シャンプー',
            'product_price' => 1000,
            'quantity' => 15,
            'remarks' => '顧客用シャンプー',
            'supplier' => '株式会社〇〇',
            'notice' => 10,
            'stock_category_id' => 2,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('stocks')->insert([
            'product_name' => 'ワックス',
            'product_price' => 800,
            'quantity' => 15,
            'remarks' => '顧客用ワックス',
            'supplier' => '株式会社〇〇',
            'notice' => 10,
            'stock_category_id' => 1,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('stocks')->insert([
            'product_name' => 'ヘアオイル',
            'product_price' => 700,
            'quantity' => 3,
            'remarks' => '顧客用ヘアオイル',
            'supplier' => '株式会社〇〇',
            'notice' => 5,
            'stock_category_id' => 3,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
