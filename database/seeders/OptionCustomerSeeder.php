<?php

namespace Database\Seeders;

use App\Models\OptionCustomer;
use Illuminate\Database\Seeder;

class OptionCustomerSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    OptionCustomer::factory()
      ->count(200)
      ->create();
  }
}
