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
        DB::table('schedules')->insert([
            'customer_id' => 1,
            'title' => null,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'allDay' => false,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('schedules')->insert([
            'customer_id' => 2,
            'title' => null,
            'start_time' => '11:00:00',
            'end_time' => '12:00:00',
            'allDay' => false,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('schedules')->insert([
            'customer_id' => 3,
            'title' => null,
            'start_time' => '13:00:00',
            'end_time' => '14:00:00',
            'allDay' => false,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
