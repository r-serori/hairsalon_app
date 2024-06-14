<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Hairstyle;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);



        $this->call([
            UserSeeder::class,
            // AttendanceSeeder::class,
            // Attendance_timesSeeder::class,
            CourseSeeder::class,
            MerchandiseSeeder::class,
            OptionSeeder::class,
            HairstyleSeeder::class,
            CustomersSeeder::class,
            StockCategoriesSeeder::class,
            StocksSeeder::class,
            ScheduleSeeder::class,
            // DailySalesSeeder::class,
            // MonthlySalesSeeder::class,
            // YearlySalesSeeder::class,






        ]);

        // \App\Models\Attendance_times::factory(50)->create();
        // \App\Models\attendances::factory(50)->create();
        // \App\Models\courses::factory(50)->create();
        // \App\Models\merchandises::factory(50)->create();
        // \App\Models\options::factory(50)->create();
        // \App\Models\hairstyles::factory(50)->create();
        // \App\Models\customers::factory(50)->create();
        // \App\Models\stock_categories::factory(50)->create();
        // \App\Models\stocks::factory(50)->create();
        // \App\Models\expense_categories::factory(50)->create();
        // \App\Models\expenses::factory(50)->create();
        // \App\Models\schedules::factory(50)->create();
        // \App\Models\daily_sales::factory(50)->create();
        // \App\Models\monthly_sales::factory(50)->create();
        // \App\Models\yearly_sales::factory(50)->create();


    }
}
