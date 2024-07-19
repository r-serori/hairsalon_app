<?php

namespace Database\Seeders;

use App\Models\customer_schedules;
use App\Models\schedules;
use App\Models\customers;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schedule::factory()
            ->count(30)
            ->create();
    }
}
