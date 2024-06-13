<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('staff')->insert([
            'position' => 'マネージャー',
            'user_id' => 2,
            'owner_id' => 1,
            'created_at' => now(),
        ]);
        DB::table('staff')->insert([
            'position' => 'スタッフ',
            'user_id' => 3,
            'owner_id' => 1,
            'created_at' => now(),
        ]);
    }
}
