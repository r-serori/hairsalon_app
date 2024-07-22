<?php

namespace Database\Seeders;

use App\Models\StockCategory;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stock_categories')->insert([
            'category' => '整髪料',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('stock_categories')->insert([
            'category' => 'シャンプー類',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('stock_categories')->insert([
            'category' => 'ヘアケア用品',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
