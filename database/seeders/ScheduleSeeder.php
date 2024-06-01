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
        // 30個のスケジュールデータを作成
        for ($i = 0; $i <= 30; $i++) {
            $schedule = schedules::create([
                'title' => '',
                'start_time' => Carbon::parse('2024-6-1 10:00:00')->addDays($i)->addHours($i),
                'end_time' => Carbon::parse('2024-6-1 11:00:00')->addDays($i)->addHours($i),
                'allDay' => 0,
            ]);

            $customerIds = rand(1, 30);
            $customerId = customers::find($customerIds);
            $customers = customers::whereIn('id', $customerId)->get();


            // 中間テーブルにデータを追加

            foreach ($customers as $customer) {
                customer_schedules::create([
                    'customers_id' => $customer->id,
                    'schedules_id' => $schedule->id,
                ]);
            }
        }
        // 30個のスケジュールデータを作成
        for ($i = 0; $i <= 30; $i++) {
            $schedule = schedules::create([
                'title' => '',
                'start_time' => Carbon::parse('2024-7-1 10:00:00')->addDays($i)->addHours($i),
                'end_time' => Carbon::parse('2024-7-1 11:00:00')->addDays($i)->addHours($i),
                'allDay' => 0,
            ]);

            $customerIds = rand(1, 30);
            $customerId = customers::find($customerIds);
            $customers = customers::whereIn('id', $customerId)->get();


            // 中間テーブルにデータを追加

            foreach ($customers as $customer) {
                customer_schedules::create([
                    'customers_id' => $customer->id,
                    'schedules_id' => $schedule->id,
                ]);
            }
        }


        // 30個のスケジュールデータを作成
        for ($i = 0; $i <= 30; $i++) {
            $schedule = schedules::create([
                'title' => '',
                'start_time' => Carbon::parse('2024-8-1 10:00:00')->addDays($i)->addHours($i),
                'end_time' => Carbon::parse('2024-8-1 11:00:00')->addDays($i)->addHours($i),
                'allDay' => 0,
            ]);

            $customerIds = rand(1, 30);
            $customerId = customers::find($customerIds);
            $customers = customers::whereIn('id', $customerId)->get();

            foreach ($customers as $customer) {
                customer_schedules::create([
                    'customers_id' => $customer->id,
                    'schedules_id' => $schedule->id,
                ]);
            }
        }
    }
}
