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
            'merchandise_name' => 'ワックス',
            'price' => 1200,
            'created_at' => now(),
        ]);
        DB::table('merchandises')->insert([
            'merchandise_name' => 'ジェル',
            'price' => 1000,
            'created_at' => now(),
        ]);
        DB::table('merchandises')->insert([
            'merchandise_name' => 'ムース',
            'price' => 1500,
            'created_at' => now(),
        ]);
        
    }
}
