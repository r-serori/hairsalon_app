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
            AttendanceSeeder::class,
            // Attendance_timesSeeder::class,
            CourseSeeder::class,
            MerchandiseSeeder::class,
            OptionSeeder::class,
            HairstyleSeeder::class,
            CustomersSeeder::class,
            StockCategoriesSeeder::class,


            

        ]);

        \App\Models\Attendance_times::factory(50)->create();

    }
}
