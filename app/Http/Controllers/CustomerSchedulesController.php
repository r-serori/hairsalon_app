<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customer_schedules;

class CustomerSchedulesController extends Controller
{
  public function index()
  {
    try {
      $customer_schedules = customer_schedules::all();

      return response()->json([
        "resStatus" => "success",
        'customer_schedules' => $customer_schedules
      ]);
    } catch (\Exception $e) {
      return response()->json([
        "resStatus" => "error",
        'message' => 'customer_schedules not found'
      ], 500);
    }
  }
}
