<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\merchandise_customers;

class MerchandiseCustomersController extends Controller
{
    public function index()
    {
        $merchandise_customers = merchandise_customers::all();

        return response()->json(['merchandise_customers' => $merchandise_customers]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'merchandises_id' => 'required',
            'customers_id' => 'required',
        ]);

        $merchandise_customers = merchandise_customers::create([
            'merchandises_id' => $validatedData['merchandises_id'],
            'customers_id' => $validatedData['customers_id'],
        ]);

        return
            response()->json(
                [],
                204
            );
    }

    public function destroy($id)
    {
        $merchandise_customers = merchandise_customers::find($id);
        if (!$merchandise_customers) {
            return response()->json(['message' =>
            'merchandise_customers not found'], 404);
        }

        try {
            $merchandise_customers->delete();
            return response()->json(
                [],
                204
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete merchandise_customers ', 'error' => $e->getMessage()], 500);
        }
    }
}
