<?php

namespace Database\Seeders;

use App\Models\MerchandiseCustomer;
use Illuminate\Database\Seeder;

class MerchandiseCustomerSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    MerchandiseCustomer::factory()
      ->count(200)
      ->create();
  }
}
