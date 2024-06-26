<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'tanaka123',
            'email' => 'aaaaa@hairmail.com',
            'password' => Hash::make('password123'),
            'role' => 'オーナー',
            'created_at' => now(),
        ]);
        // DB::table('users')->insert([
        //     'login_id' => 'tarou123',
        //     'password' => Hash::make('password123'),
        //     'created_at' => now(),
        // ]);
    }
}