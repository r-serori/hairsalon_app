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
            'name' => 'owner123',
            'email' => 'owner@hairmail.com',
            'phone_number' => '090-1234-5678',
            'password' => Hash::make('password123'),
            'role' => 'オーナー',
            'isAttendance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'manager123',
            'email' => 'manager@hairmail.com',
            'phone_number' => '080-1234-5678',
            'password' => Hash::make('password123'),
            'role' => 'マネージャー',
            'isAttendance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'name' => 'staff123',
            'email' => 'staff@hairmail.com',
            'phone_number' => '070-1234-5678',
            'password' => Hash::make('password123'),
            'role' => 'スタッフ',
            'isAttendance' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
