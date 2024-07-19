<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonthlySale;

class MonthlySaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MonthlySale::factory()
            ->count(80)
            ->create();
    }
}
