<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\owner;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('courses')->insert([
            'course_name' => 'カットとシェービングA',
            'price' => 7000,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('courses')->insert([
            'course_name' => 'カットとシェービングB',
            'price' => 6500,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('courses')->insert([
            'course_name' => 'カットとシェービングC',
            'price' => 6000,
            'owner_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
