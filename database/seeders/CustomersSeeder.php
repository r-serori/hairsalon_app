<?php

namespace Database\Seeders;

use App\Models\customers;
use App\Models\course_customers; // course_customers モデルを追加
use App\Models\hairstyle_customers; // hairstyle_customers モデルを追加
use App\Models\option_customers; // option_customers モデルを追加
use App\Models\merchandise_customers; // merchandise_customers モデルを追加
use App\Models\hairstyles;
use App\Models\options;
use App\Models\courses;
use App\Models\merchandises;
use App\Models\attendances;
use App\Models\customer_attendances; // customer_attendance モデルを追加

use Illuminate\Database\Seeder;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 30個の顧客データを作成
        for ($i = 1; $i <= 30; $i++) {
            $customer = customers::create([
                'customer_name' => 'Customer ' . $i,
                'phone_number' => '123456789' . $i,
                'remarks' => 'Regular customer ' . $i,
                'new_customer' => '既存', // 奇数番目は新規顧客、偶数番目は既存顧客とする
            ]);

            // ハンドリングするヘアスタイルを取得
            $hairstyles = hairstyles::whereIn('id', [1, 2])->get();

            // 中間テーブルにデータを追加
            foreach ($hairstyles as $hairstyle) {
                hairstyle_customers::create([
                    'customers_id' => $customer->id,
                    'hairstyles_id' => $hairstyle->id,
                ]);
            }

            // ハンドリングするコースを取得
            $courses = courses::whereIn('id', [1, 2])->get();

            // 中間テーブルにデータを追加
            foreach ($courses as $course) {
                course_customers::create([
                    'customers_id' => $customer->id,
                    'courses_id' => $course->id,
                ]);
            }

            // ハンドリングするオプションを取得
            $options = options::whereIn('id', [1, 2])->get();

            // 中間テーブルにデータを追加
            foreach ($options as $option) {
                option_customers::create([
                    'customers_id' => $customer->id,
                    'options_id' => $option->id,
                ]);
            }

            // ハンドリングする商品を取得
            $merchandises = merchandises::whereIn('id', [1, 2])->get();

            // 中間テーブルにデータを追加
            foreach ($merchandises as $merchandise) {
                merchandise_customers::create([
                    'customers_id' => $customer->id,
                    'merchandises_id' => $merchandise->id,
                ]);
            }

            // ハンドリングする出席を取得
            $attendances = attendances::whereIn('id', [1, 2])->get();

            // 中間テーブルにデータを追加
            foreach ($attendances as $attendance) {
                customer_attendances::create([
                    'customers_id' => $customer->id,
                    'attendances_id' => $attendance->id,
                ]);
            }
        }
    }
}
