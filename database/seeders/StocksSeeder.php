<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StocksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\stocks::create([
            'product_name' => 'ワックス',
            'product_price' => '1000',
            'quantity' => '10',
            'remarks' => '化粧品',
            'supplier' => '化粧品',
            'stock_category_id' => '3',
            'created_at' => '2021-01-01 00:00:00'
        ]);
        \App\Models\stocks::create([
            'product_name' => 'シャンプー',
            'product_price' => '3000',
            'quantity' => '3',
            'remarks' => '頭髪系',
            'supplier' => '頭髪系',
            'stock_category_id' => '2',
            'created_at' => '2021-01-01 00:00:00'
        ]);
        \App\Models\stocks::create([
            'product_name' => '乳液',
            'product_price' => '2000',
            'quantity' => '4',
            'remarks' => '乳液',
            'supplier' => '乳液',
            'stock_category_id' => '1',
            'created_at' => '2021-01-01 00:00:00'
        ]);
        \App\Models\stocks::create([
            'product_name' => 'CB-5G',
            'product_price' => '1000',
            'quantity' => '5',
            'remarks' => 'CB-5G',
            'supplier' => 'CB-5G',
            'stock_category_id' => '4',
            'created_at' => '2021-01-01 00:00:00'
        ]);
        \App\Models\stocks::create([
            'product_name' => 'TG77',
            'product_price' => '1000',
            'quantity' => '20',
            'remarks' => 'TG77',
            'supplier' => 'TG77',
            'stock_category_id' => '5',
            'created_at' => '2021-01-01 00:00:00'
        ]);
        \App\Models\stocks::create([
            'product_name' => 'コーラ',
            'product_price' => '100',
            'quantity' => '100',
            'remarks' => 'コーラ',
            'supplier' => 'コーラ',
            'stock_category_id' => '6',
            'created_at' => '2021-01-01 00:00:00'
        ]);
    
    }
}
