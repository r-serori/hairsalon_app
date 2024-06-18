<?php

namespace Database\Seeders;

use App\Models\customer_schedules;
use App\Models\schedules;
use App\Models\customers;
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

        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addDay($i),
                'end_time' => Carbon::now()->addHours(1)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'owner_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(2)->addDay($i),
                'end_time' => Carbon::now()->addHours(2)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'owner_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(3)->addDay($i),
                'end_time' => Carbon::now()->addHours(3)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'owner_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(4)->addDay($i),
                'end_time' => Carbon::now()->addHours(4)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'owner_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(5)->addDay($i),
                'end_time' => Carbon::now()->addHours(5)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'owner_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(6)->addDay($i),
                'end_time' => Carbon::now()->addHours(6)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'owner_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(7)->addDay($i),
                'end_time' => Carbon::now()->addHours(7)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(8)->addDay($i),
                'end_time' => Carbon::now()->addHours(8)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(9)->addDay($i),
                'end_time' => Carbon::now()->addHours(9)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(10)->addDay($i),
                'end_time' => Carbon::now()->addHours(10)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(11)->addDay($i),
                'end_time' => Carbon::now()->addHours(11)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
        for ($i = 1; $i <= 30; $i++) {
            DB::table('schedules')->insert([
                'title' => '',
                'start_time' => Carbon::now()->addHours(12)->addDay($i),
                'end_time' => Carbon::now()->addHours(12)->addDay($i),
                'allDay' => 0,
                'customers_id' => $i,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        DB::table('schedules')->insert([
            'title' => '郵便',
            'start_time' => '2024-10-04 0:00:00',
            'end_time' => '2024-10-05 0:00:00',
            'allDay' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('schedules')->insert([
            'title' => '',
            'start_time' => '2020-09-01 10:00:00',
            'end_time' => '2020-09-01 11:00:00',
            'allDay' => 0,
            'customers_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('schedules')->insert([
            'title' => '',
            'start_time' => '2021-09-01 10:00:00',
            'end_time' => '2021-09-01 11:00:00',
            'allDay' => 0,
            'customers_id' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('schedules')->insert([
            'title' => '',
            'start_time' => "2025-09-01 10:00:00", // "2021-09-01 10:00:00
            'end_time' => "2025-09-01 11:00:00", // "2021-09-01 11:00:00
            'allDay' => 0,
            'customers_id' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('schedules')->insert([
            'title' => '',
            'start_time' => "2026-09-01 10:00:00", // "2021-09-01 10:00:00
            'end_time' => "2026-09-01 11:00:00", // "2021-09-01 11:00:00
            'allDay' => 0,
            'customers_id' => 4,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('schedules')->insert([
            'title' => '',
            'start_time' => "2027-09-01 10:00:00", // "2021-09-01 10:00:00
            'end_time' => "2027-09-01 11:00:00", // "2021-09-01 11:00:00
            'allDay' => 0,
            'customers_id' => 5,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
