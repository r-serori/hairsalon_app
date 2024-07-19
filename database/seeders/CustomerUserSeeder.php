<?php

namespace Database\Seeders;

use App\Models\CustomerUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerUserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    CustomerUser::factory()
      ->count(200)
      ->create();
  }
}
