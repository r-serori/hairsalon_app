<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customer_schedules;

class CustomerSchedulesController extends Controller
{
  public function index()
  {
    $customer_schedules = customer_schedules::all();

    return response()->json(['customer_schedules' => $customer_schedules]);
  }

  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'customers_id' => 'required',
      'schedules_id' => 'required',
    ]);

    $customer_schedules = customer_schedules::create([
      'customers_id' => $validatedData['customers_id'],
      'schedules_id' => $validatedData['schedules_id'],
    ]);

    return
      response()->json(
        [],
        204
      );
  }

  public function destroy($id)
  {
    $customer_schedules = customer_schedules::find($id);
    if (!$customer_schedules) {
      return response()->json(['message' =>
      'customer_schedules not found'], 404);
    }

    try {
      $customer_schedules->delete();
      return response()->json(
        [],
        204
      );
    } catch (\Exception $e) {
      return response()->json(['message' => 'Failed to delete customer_schedules ', 'error' => $e->getMessage()], 500);
    }
  }
}
