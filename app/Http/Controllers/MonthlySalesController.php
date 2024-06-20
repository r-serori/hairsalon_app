<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\monthly_sales;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;


class MonthlySalesController extends Controller
{
    public function index($id)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
                // 月別売上一覧を取得
                $monthly_sales = monthly_sales::where('owner_id', $id)->get();
                if ($monthly_sales->isEmpty()) {
                    return response()->json([
                        "resStatus" => "success",
                        "message" =>
                        "初めまして！予約表画面の月次売上更新ボタンから月次売上を作成しましょう！",
                        'monthlySales' => $monthly_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        "resStatus" => "success",
                        'monthlySales' => $monthly_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                '月次売上が見つかりません。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function store(Request $request)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
                // バリデーションルールを定義する
                $validatedData = $request->validate([
                    'year_month' => 'required|string',
                    'monthly_sales' => 'required|integer',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                // 月別売上モデルを作成して保存する
                $monthly_sales = monthly_sales::create([
                    'year_month' => $validatedData['year_month'],
                    'monthly_sales' => $validatedData['monthly_sales'],
                    'owner_id' => $validatedData['owner_id'],
                ]);

                // 成功したらリダイレクト
                return response()->json([
                    "resStatus" => "success",
                    "monthlySale" => $monthly_sales
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "月次売上の作成に失敗しました。"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    // public function show($id)
    // {
    //     try {
    //         if (Gate::allows(Permissions::OWNER_PERMISSION)) {
    //             // 指定されたIDの月別売上を取得
    //             $monthly_sale = monthly_sales::find($id);

    //             // 月別売上を表示
    //             return response()->json([
    //                 "resStatus" => "success",
    //                 'monthlySale' => $monthly_sale
    //             ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         } else {
    //             return response()->json([
    //                 "resStatus" => "error",
    //                 "message" => "権限がありません"
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             'message' =>
    //             '月次売上が見つかりません。'
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }



    public function update(Request $request)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
                // バリデーションルールを定義する
                $validatedData = $request->validate([
                    'year_month' => 'required|string',
                    'monthly_sales' => 'required|integer',
                ]);

                // 月別売上を取得する
                $monthly_sale = monthly_sales::find($request->id);

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
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "月次売上の更新に失敗しました。"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
                $monthly_sale = monthly_sales::find($request->id);
                if (!$monthly_sale) {
                    return response()->json([
                        "resStatus" => "error",
                        'message' =>
                        '月次売上が見つかりません。'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $monthly_sale->delete();
                return response()->json([
                    "resStatus" => "success",
                    "deleteId" => $request->id

                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                '月次売上が見つかりません。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
