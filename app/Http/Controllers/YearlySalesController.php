<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\yearly_sales;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;

class YearlySalesController extends Controller
{
    public function index($id)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
                $yearly_sales = yearly_sales::all();
                if ($yearly_sales->isEmpty()) {
                    return response()->json([
                        "resStatus" => "success",
                        "message" =>
                        "初めまして！予約表画面の月次売上作成ボタンから月次売上を作成しましょう！",
                        'yearlySales' => $yearly_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        "resStatus" => "success",
                        'yearlySales' => $yearly_sales
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

    public function store(Request $request, $id)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
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
    //             $yearly_sale = yearly_sales::find($id);

    //             return response()->json([
    //                 "resStatus" => "success",
    //                 'yearlySale' => $yearly_sale
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



    public function update(Request $request, $id)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
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
                'message' =>
                '月次売上が見つかりません。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy($id)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
                $yearly_sale = yearly_sales::find($id);
                if (!$yearly_sale) {
                    return response()->json([
                        "resStatus" => "error",
                        'message' =>
                        '月次売上が見つかりません。'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $yearly_sale->delete();
                return response()->json([
                    "resStatus" => "success",
                    "deleteId" => $id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json(['message' =>
            'monthly_sale not found'], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
