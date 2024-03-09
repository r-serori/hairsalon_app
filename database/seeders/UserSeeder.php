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
            'name' => '田中店長',
            'position' => 'オーナー',
            'phone_number' => '090-1234-5678',
            'password' => Hash::make('password123'),
            'created_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => '太朗社員',
            'position' => '社員',
            'phone_number' => '090-1234-5678',
            'password' => Hash::make('password1234'),
            'created_at' => now(),
        ]);
    }
}
