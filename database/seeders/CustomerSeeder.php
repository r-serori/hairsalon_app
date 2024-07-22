<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('customers')->insert([
            'customer_name' => '山田太郎',
            'phone_number' => '00000001111',
            'remarks' => '初めての来店',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('customers')->insert([
            'customer_name' => '山田花子',
            'phone_number' => '00000001112',
            'remarks' => 'リピーター',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('customers')->insert([
            'customer_name' => '山田次郎',
            'phone_number' => '00000001113',
            'remarks' => '山田太郎さんの息子',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('customers')->insert([
            'customer_name' => '山田三郎',
            'phone_number' => '00000001114',
            'remarks' => '山田太郎さんの息子',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('customers')->insert([
            'customer_name' => '佐藤四郎',
            'phone_number' => '00000001115',
            'remarks' => '佐藤商店の社長',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('customers')->insert([
            'customer_name' => '佐藤五郎',
            'phone_number' => '00000001116',
            'remarks' => '佐藤商店の社員',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('customers')->insert([
            'customer_name' => '佐藤六郎',
            'phone_number' => '00000001117',
            'remarks' => '佐藤商店の社員',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('customers')->insert([
            'customer_name' => '鈴木七郎',
            'phone_number' => '00000001118',
            'remarks' => '近所の人',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('customers')->insert([
            'customer_name' => '鈴木八郎',
            'phone_number' => '00000001119',
            'remarks' => '気難しい',
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 顧客を30人作成
        for ($i = 1; $i <= 30; $i++) {
            Customer::create([
                'customer_name' => '顧客' . $i,
                'phone_number' => '0000000' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'remarks' => '顧客' . $i . 'のメモ',
                'owner_id' => 1, // 適切なowner_idを設定
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 中間テーブルにもデータを追加
        $customers = Customer::all();

        foreach ($customers as $customer) {
            // course_customers
            DB::table('course_customers')->insert([
                'course_id' => rand(1, 3), // 適切なcourse_idを設定
                'customer_id' => $customer->id,
                'owner_id' => $customer->owner_id,

            ]);

            // option_customers
            DB::table('option_customers')->insert([
                'option_id' => rand(1, 3), // 適切なoption_idを設定
                'customer_id' => $customer->id,
                'owner_id' => $customer->owner_id,
            ]);

            // merchandise_customers
            DB::table('merchandise_customers')->insert([
                'merchandise_id' => rand(1, 3), // 適切なmerchandise_idを設定
                'customer_id' => $customer->id,
                'owner_id' => $customer->owner_id,
            ]);


            // hairstyle_customers
            DB::table('hairstyle_customers')->insert([
                'hairstyle_id' => rand(1, 3), // 適切なhairstyle_idを設定
                'customer_id' => $customer->id,
                'owner_id' => $customer->owner_id,
            ]);



            // customer_users
            DB::table('customer_users')->insert([
                'user_id' => rand(1, 3), // 適切なuser_idを設定
                'customer_id' => $customer->id,
                'owner_id' => $customer->owner_id,
            ]);
        }
    }
}
