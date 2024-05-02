<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\yearly_sales;

class YearlySalesController extends Controller
{
    public function index()
    {
        $yearly_sales = yearly_sales::all();
        return response()->json(['yearly_sales' => $yearly_sales]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'year' => 'required|integer',
            'yearly_sales' => 'required|integer',
        ]);

        yearly_sales::create([
            'year' => $validatedData['year'],
            'yearly_sales' => $validatedData['yearly_sales'],
        ]);

        return response()->json([], 204);
    }

    public function show($id)
    {
        $yearly_sale = yearly_sales::find($id);

        return response()->json(['yearly_sale' => $yearly_sale]);
    }

    public function edit($id)
    {
        $yearly_sale = yearly_sales::find($id);

        if (!$yearly_sale) {
            return response()->json(['message' =>
            'yearly_sale not found'], 404);
        }

        return response()->json(['yearly_sale' => $yearly_sale]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'year' => 'required|integer',
            'yearly_sales' => 'required|integer',
        ]);

        $yearly_sale = yearly_sales::find($id);

        $yearly_sale->year = $validatedData['year'];
        $yearly_sale->yearly_sales = $validatedData['yearly_sales'];
        $yearly_sale->save();

        return response()->json(
            [],
            204
        );
    }

    public function destroy($id)
    {
        $yearly_sale = yearly_sales::find($id);
        if (!$yearly_sale) {
            return response()->json(['message' =>
            'monthly_sale not found'], 404);
        }


        try {
            $yearly_sale->delete();
            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json(['message' =>
            'monthly_sale not found'], 404);
        }
    }
}
