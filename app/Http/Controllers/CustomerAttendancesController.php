<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customer_attendances;

class CustomerAttendancesController extends Controller
{
  public function index()
  {
    $customer_attendances = customer_attendances::all();
    return response()->json(['customer_attendances' => $customer_attendances]);
  }

  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'customers_id' => 'required',
      'attendances_id' => 'required',
    ]);

    customer_attendances::create([
      'customers_id' => $validatedData['customers_id'],
      'attendances_id' => $validatedData['attendances_id'],

    ]);

    return response()->json([], 204);
  }


  public function update(Request $request, $id)
  {
    $validatedData = $request->validate([
      'customers_id' => 'required',
      'attendances_id' => 'required',
    ]);

    $customer_attendances = customer_attendances::find($id);

    $customer_attendances->customers_id = $validatedData['customers_id'];
    $customer_attendances->attendances_id = $validatedData['attendances_id'];

    $customer_attendances->save();

    return response()->json(
      ['customer_attendances' => $customer_attendances]
    );
  }

  public function destroy($id)
  {
    $customer_attendances = customer_attendances::find($id);
    if (!$customer_attendances) {
      return response()->json(['message' =>
      'customer_attendances not found'], 404);
    }

    try {
      $customer_attendances->delete();
      return response()->json([], 204);
    } catch (\Exception $e) {
      return response()->json(['message' => $e->getMessage()], 500);
    }
  }
}
