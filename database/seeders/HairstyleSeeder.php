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
        Hairstyle::factory()
            ->count(10)
            ->create();
    }
}
