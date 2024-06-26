<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StocksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stocks')->insert([
            'product_name' => 'Tシャツ',
            'product_price' => 1000,
            'quantity' => 10,
            'remarks' => '新商品',
            'supplier' => '株式会社A',
            "notice" => "5",
            'stock_category_id' => 1,
            'owner_id' => 1,
            'created_at' => now(),
            "updated_at" => now(),
        ]);
        DB::table('stocks')->insert([
            'product_name' => 'スウェット',
            'product_price' => 2000,
            'quantity' => 20,
            'remarks' => '新商品',
            'supplier' => '株式会社B',
            "notice" => "10",
            'stock_category_id' => 2,
            'owner_id' => 1,
            'created_at' => now(),
            "updated_at" => now(),
        ]);
        DB::table('stocks')->insert([
            'product_name' => 'キャップ',
            'product_price' => 2000,
            'quantity' => 20,
            'remarks' => 'なし',
            'supplier' => '株式会社B',
            "notice" => "10",
            'stock_category_id' => null,
            'owner_id' => 1,
            'created_at' => now(),
            "updated_at" => now(),
        ]);
    }
}
