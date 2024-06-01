<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\yearly_sales;

class YearlySalesController extends Controller
{
    public function index()
    {
        try {
            $yearly_sales = yearly_sales::all();
            return response()->json([
                "resStatus" => "success",
                'yearlySales' => $yearly_sales
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                '月次売上が見つかりません。'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'year' => 'required|string',
                'yearly_sales' => 'required|integer',
            ]);

            $yearly_sale = yearly_sales::create([
                'year' => $validatedData['year'],
                'yearly_sales' => $validatedData['yearly_sales'],
            ]);

            return response()->json([
                "resStatus" => "success",
                "yearlySale" => $yearly_sale
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "月次売上の作成に失敗しました。"
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $yearly_sale = yearly_sales::find($id);

            return response()->json([
                "resStatus" => "success",
                'yearlySale' => $yearly_sale
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                '月次売上が見つかりません。'
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'year' => 'required|string',
                'yearly_sales' => 'required|integer',
            ]);

            $yearly_sale = yearly_sales::find($id);

            $yearly_sale->year = $validatedData['year'];
            $yearly_sale->yearly_sales = $validatedData['yearly_sales'];
            $yearly_sale->save();

            return response()->json(
                [
                    "resStatus" => "success",
                    "yearlySale" => $yearly_sale
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                '月次売上が見つかりません。'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $yearly_sale = yearly_sales::find($id);
            if (!$yearly_sale) {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    '月次売上が見つかりません。'
                ], 500);
            }

            $yearly_sale->delete();
            return response()->json([
                "resStatus" => "success",
                "deleteId" => $id
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' =>
            'monthly_sale not found'], 500);
        }
    }
}
