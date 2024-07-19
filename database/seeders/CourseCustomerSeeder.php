<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCustomer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseCustomerSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    CourseCustomer::factory()
      ->count(20)
      ->create();
  }
}
