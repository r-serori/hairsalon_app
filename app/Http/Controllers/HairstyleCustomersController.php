<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\hairstyle_customers;

class HairstyleCustomersController extends Controller
{
    public function index()
    {
        $hairstyle_customers = hairstyle_customers::all();

        return response()->json(['hairstyle_customers' => $hairstyle_customers]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'hairstyles_id' => 'required',
            'customers_id' => 'required',
        ]);

        $hairstyle_customers = hairstyle_customers::create([
            'hairstyles_id' => $validatedData['hairstyles_id'],
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
        $hairstyle_customers = hairstyle_customers::find($id);
        if (!$hairstyle_customers) {
            return response()->json(['message' =>
            'hairstyle_customers not found'], 404);
        }

        try {
            $hairstyle_customers->delete();

            return response()->json(
                [],
                204
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete hairstyle_customers ', 'error' => $e->getMessage()], 500);
        }
    }
}
