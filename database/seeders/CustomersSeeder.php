<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\customers;
use App\Models\course_customers; // course_customers モデルを追加
use App\Models\CourseCustomer;
use App\Models\hairstyle_customers; // hairstyle_customers モデルを追加
use App\Models\option_customers; // option_customers モデルを追加
use App\Models\merchandise_customers; // merchandise_customers モデルを追加
use App\Models\hairstyles;
use App\Models\options;
use App\Models\courses;
use App\Models\Customer;
use App\Models\merchandises;
use App\Models\User;
use App\Models\customer_users; // customer_user モデルを追加
use App\Models\CustomerUser;
use App\Models\Hairstyle;
use App\Models\HairstyleCustomer;
use App\Models\Merchandise;
use App\Models\MerchandiseCustomer;
use App\Models\Option;
use App\Models\OptionCustomer;
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
            $customer = Customer::create([
                'customer_name' => 'Customer ' . $i,
                'phone_number' => '123456789' . $i,
                'remarks' => 'Regular customer ' . $i,
                'owner_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),

            ]);

            // ハンドリングするヘアスタイルを取得
            $hairstyles = Hairstyle::whereIn('id', [1, 2])->get();

            // 中間テーブルにデータを追加
            foreach ($hairstyles as $hairstyle) {
                HairstyleCustomer::create([
                    'customer_id' => $customer->id,
                    'hairstyle_id' => $hairstyle->id,
                    'owner_id' => 1,


                ]);
            }

            // ハンドリングするコースを取得
            $courses = Course::whereIn('id', [1, 2])->get();

            // 中間テーブルにデータを追加
            foreach ($courses as $course) {
                CourseCustomer::create([
                    'customer_id' => $customer->id,
                    'course_id' => $course->id,
                    'owner_id' => 1,

                ]);
            }

            // ハンドリングするオプションを取得
            $options = Option::whereIn('id', [1, 2])->get();

            // 中間テーブルにデータを追加
            foreach ($options as $option) {
                OptionCustomer::create([
                    'customer_id' => $customer->id,
                    'option_id' => $option->id,
                    'owner_id' => 1,


                ]);
            }

            // ハンドリングする商品を取得
            $merchandises = Merchandise::whereIn('id', [1, 2])->get();

            // 中間テーブルにデータを追加
            foreach ($merchandises as $merchandise) {
                MerchandiseCustomer::create([
                    'customer_id' => $customer->id,
                    'merchandise_id' => $merchandise->id,
                    'owner_id' => 1,


                ]);
            }

            // ハンドリングする出席を取得
            $users = User::whereIn('id', [1, 2])->get();

            // 中間テーブルにデータを追加
            foreach ($users as $user) {
                CustomerUser::create([
                    'customer_id' => $customer->id,
                    'user_id' => $user->id,
                    'owner_id' => 1,


                ]);
            }
        }
    }
}
