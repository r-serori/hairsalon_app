<?php

namespace App\Http\Controllers;

use App\Models\daily_sales;
use Illuminate\Http\Request;

class DailySalesController extends Controller
{

    public function index()
    {
        try {
            $daily_sales = daily_sales::all();
            if ($daily_sales->isEmpty()) {
                return response()->json([
                    "resStatus" => "success",
                    "message" => "初めまして！予約表画面の日次売上作成ボタンから日次売上を作成しましょう！",
                    'dailySales' => $daily_sales
                ], 200);
            } else {
                return response()->json([
                    "resStatus" => "success",
                    'dailySales' => $daily_sales
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    "resStatus" => "error",
                    'message' =>
                    '日次売上が見つかりません。'
                ],
                500
            );
        }
    }


    public function store(Request $request)
    {
        try {
            $validatedData
                = $request->validate([
                    'date' => 'required',
                    'daily_sales' => 'required',
                ]);

            $daily_sales =
                daily_sales::create([
                    'date' => $validatedData['date'],
                    'daily_sales' => $validatedData['daily_sales'],
                ]);

            return response()->json([
                "resStatus" => "success",
                "dailySale" => $daily_sales
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "日次売上の作成に失敗しました。"
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $daily_sale = daily_sales::find($id);

            return response()->json([
                "resStatus" => "success",
                'dailySale' => $daily_sale
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "日次売上が見つかりません。"
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'date' => 'required',
                'daily_sales' => 'required',
            ]);

            $daily_sale = daily_sales::find($id);

            $daily_sale->date = $validatedData['date'];
            $daily_sale->daily_sales = $validatedData['daily_sales'];
            $daily_sale->save();


            return response()->json(
                [
                    "resStatus" => "success",
                    "dailySale" => $daily_sale
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "日次売上の更新に失敗しました。"
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $daily_sale = daily_sales::find($id);
            if (!$daily_sale) {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    '日次売上が見つかりません。'
                ], 500);
            }
            $daily_sale->delete();
            return response()->json([
                "resStatus" => "success",
                "deleteId" => $id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                '日次売上の削除に失敗しました。'
            ], 500);
        }
    }
}
