<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\option_customers;

class OptionCustomersController extends Controller
{

    public function index()
    {
        $option_customers = option_customers::all();

        return response()->json(['option_customers' => $option_customers]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'options_id' => 'required',
            'customers_id' => 'required',
        ]);

        $option_customers = option_customers::create([
            'options_id' => $validatedData['options_id'],
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
        $option_customers = option_customers::find($id);
        if (!$option_customers) {
            return response()->json(['message' =>
            'option_customers not found'], 404);
        }

        try {
            $option_customers->delete();
            return response()->json(
                [],
                204
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete option_customers ', 'error' => $e->getMessage()], 500);
        }
    }
}
