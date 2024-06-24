<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            'option_name' => 'エステA',
            'price' => 3000,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('options')->insert([
            'option_name' => 'エステB',
            'price' => 500,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('options')->insert([
            'option_name' => 'クレンジング',
            'price' => 1000,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
