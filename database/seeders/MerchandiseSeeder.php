<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Merchandise;
use Illuminate\Support\Facades\DB;

class MerchandiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('merchandises')->insert([
            'merchandise_name' => 'シャンプー',
            'price' => 2000,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('merchandises')->insert([
            'merchandise_name' => 'ワックス',
            'price' => 1500,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('merchandises')->insert([
            'merchandise_name' => 'ヘアオイル',
            'price' => 1200,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
