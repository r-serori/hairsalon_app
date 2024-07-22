<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Option;
use Illuminate\Support\Facades\DB;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('options')->insert([
            'option_name' => 'ヘッドスパ',
            'price' => 2000,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('options')->insert([
            'option_name' => '脱毛',
            'price' => 3000,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('options')->insert([
            'option_name' => 'マッサージ',
            'price' => 1500,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
