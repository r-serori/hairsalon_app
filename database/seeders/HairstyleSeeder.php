<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hairstyle;
use Illuminate\Support\Facades\DB;

class HairstyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hairstyles')->insert([
            'hairstyle_name' => 'ワンレングス',
            'owner_id' => 1,
            'created_at' => now(),
        ]);
        DB::table('hairstyles')->insert([
            'hairstyle_name' => 'ボブ',
            'owner_id' => 1,
            'created_at' => now(),
        ]);
        DB::table('hairstyles')->insert([
            'hairstyle_name' => 'レイヤー',
            'owner_id' => 1,
            'created_at' => now(),
        ]);
        DB::table('hairstyles')->insert([
            'hairstyle_name' => '2ブロック',
            'owner_id' => 1,
            'created_at' => now(),
        ]);
        DB::table('hairstyles')->insert([
            'hairstyle_name' => 'モヒカン',
            'owner_id' => 1,
            'created_at' => now(),
        ]);
    }
}
