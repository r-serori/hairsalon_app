<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\monthly_sales;
use App\Models\yearly_sales;


class MonthlySalesController extends Controller
{
    public function index()
    {
        try {
            // 月別売上一覧を取得
            $monthly_sales = monthly_sales::all();

            // 月別売上一覧ページにデータを渡して表示
            return response()->json([
                "resStatus" => "success",
                'monthlySales' => $monthly_sales
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
            // バリデーションルールを定義する
            $validatedData = $request->validate([
                'year_month' => 'required|string',
                'monthly_sales' => 'required|integer',
            ]);

            // 月別売上モデルを作成して保存する
            $monthly_sales = monthly_sales::create([
                'year_month' => $validatedData['year_month'],
                'monthly_sales' => $validatedData['monthly_sales'],
            ]);

            // 成功したらリダイレクト
            return response()->json([
                "resStatus" => "success",
                "monthlySale" => $monthly_sales
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
            // 指定されたIDの月別売上を取得
            $monthly_sale = monthly_sales::find($id);

            // 月別売上を表示
            return response()->json([
                "resStatus" => "success",
                'monthlySale' => $monthly_sale
            ], 200);
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
            // バリデーションルールを定義する
            $validatedData = $request->validate([
                'year_month' => 'required|string',
                'monthly_sales' => 'required|integer',
            ]);

            // 月別売上を取得する
            $monthly_sale = monthly_sales::find($id);

            // 月別売上を更新する
            $monthly_sale->year = $validatedData['year_month'];
            $monthly_sale->monthly_sales = $validatedData['monthly_sales'];
            $monthly_sale->save();

            // 成功したらリダイレクト
            return response()->json(
                [
                    "resStatus" => "success",
                    "monthlySale" => $monthly_sale
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "月次売上の更新に失敗しました。"
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $monthly_sale = monthly_sales::find($id);
            if (!$monthly_sale) {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    '月次売上が見つかりません。'
                ], 500);
            }

            $monthly_sale->delete();
            return response()->json([
                "resStatus" => "success",
                "deleteId" => $id

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                '月次売上が見つかりません。'
            ], 500);
        }
    }
}
