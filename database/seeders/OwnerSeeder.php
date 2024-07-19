<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("owners")->insert([
            'store_name' => 'HairSalon_Tanaka',
            'postal_code' => '123-4567',
            'prefecture' => '大阪府',
            'city' => '大阪市中央区',
            'addressLine1' => 'こんにちは町1-2-3',
            'addressLine2' => 'ビル101号室',
            'phone_number' => '03-1234-5678',
            'user_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Owner::factory()
        //     ->count(10)
        //     ->create();
    }
}
