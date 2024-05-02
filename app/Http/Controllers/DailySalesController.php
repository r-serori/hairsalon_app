<?php

namespace App\Http\Controllers;

use App\Models\daily_sales;
use Illuminate\Http\Request;

class DailySalesController extends Controller
{

    public function index()
    {
        $daily_sales = daily_sales::all();
        return response()->json(['daily_sales' => $daily_sales]);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'daily_sales' => 'required',
        ]);

        daily_sales::create([

            'date' => $validatedData['date'],
            'daily_sales' => $validatedData['daily_sales'],
        ]);

        return response()->json([], 204);
    }

    public function show($id)
    {
        $daily_sale = daily_sales::find($id);

        return response()->json(['daily_sale' => $daily_sale]);
    }

    public function edit($id)
    {
        $daily_sale = daily_sales::find($id);

        if (!$daily_sale) {
            return response()->json(['message' =>
            'daily_sale not found'], 404);
        }

        return response()->json(['daily_sale' => $daily_sale]);
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'daily_sales' => 'required',
        ]);

        $daily_sale = daily_sales::find($id);

        $daily_sale->date = $validatedData['date'];
        $daily_sale->daily_sales = $validatedData['daily_sales'];
        $daily_sale->save();


        return response()->json(
            [],
            204
        );
    }

    public function destroy($id)
    {
        $daily_sale = daily_sales::find($id);
        if (!$daily_sale) {
            return response()->json(['message' =>
            'daily_sale not found'], 404);

            try {
                $daily_sale->delete();
                return response()->json([], 204);
            } catch (\Exception $e) {
                return response()->json(['message' =>
                'daily_sale not found'], 404);
            }
        }
    }
}
