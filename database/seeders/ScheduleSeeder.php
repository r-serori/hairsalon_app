<?php

namespace Database\Seeders;

use App\Models\customer_schedules;
use App\Models\schedules;
use App\Models\customers;
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
        for ($i = 0; $i <= 120; $i++) {
            $schedule = schedules::create([
                'title' => '',
                'start_time' => now()->addDays($i),
                'end_time' => now()->addDays($i)->addHours(1),
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
        for ($i = 0; $i <= 120; $i++) {
            $schedule = schedules::create([
                'title' => '',
                'start_time' => now()->addDays($i)->addHours(1),
                'end_time' => now()->addDays($i)->addHours(2),
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
        for ($i = 0; $i <= 120; $i++) {
            $schedule = schedules::create([
                'title' => '',
                'start_time' => now()->addDays($i)->addHours(2),
                'end_time' => now()->addDays($i)->addHours(3),
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
        for ($i = 0; $i <= 120; $i++) {
            $schedule = schedules::create([
                'title' => '',
                'start_time' => now()->addDays($i)->addHours(3),
                'end_time' => now()->addDays($i)->addHours(4),
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
        for ($i = 0; $i <= 120; $i++) {
            $schedule = schedules::create([
                'title' => '',
                'start_time' => now()->addDays($i)->addHours(4),
                'end_time' => now()->addDays($i)->addHours(5),
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
        for ($i = 0; $i <= 120; $i++) {
            $schedule = schedules::create([
                'title' => '',
                'start_time' => now()->addDays($i)->addHours(5),
                'end_time' => now()->addDays($i)->addHours(6),
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
        for ($i = 0; $i <= 120; $i++) {
            $schedule = schedules::create([
                'title' => '',
                'start_time' => now()->addDays($i)->addHours(6),
                'end_time' => now()->addDays($i)->addHours(7),
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
    }
}
