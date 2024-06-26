<?php

namespace App\Http\Controllers;

use App\Models\daily_sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;

class DailySalesController extends Controller
{

    public function index($id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {

                $user_id = urldecode($id);

                $daily_sales = daily_sales::where('owner_id', $user_id)->get();
                if ($daily_sales->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！予約表画面の日次売上作成ボタンから日次売上を作成しましょう！",
                        'dailySales' => $daily_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'dailySales' => $daily_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' =>
                    '日次売上が見つかりません！
                    もう一度お試しください！'
                ],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            )->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $validatedData
                    = $request->validate([
                        'date' => 'required',
                        'daily_sales' => 'required',
                        'owner_id' => 'required|integer|exists:owners,id',
                    ]);

                $daily_sales =
                    daily_sales::create([
                        'date' => $validatedData['date'],
                        'daily_sales' => $validatedData['daily_sales'],
                        'owner_id' => $validatedData['owner_id'],
                    ]);

                return response()->json([
                    "dailySale" => $daily_sales
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "日次売上の作成に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    // public function show($id)
    // {
    //     try {
    //         if (Gate::allows(Permissions::OWNER_PERMISSION)) {
    //             $daily_sale = daily_sales::find($id);

    //             return response()->json([
    //                 'dailySale' => $daily_sale
    //             ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         } else {
    //             return response()->json([
    //                 "message" => "あなたには権限が！"
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "message" => "日次売上が見つかりません！"
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }


    public function update(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $validatedData = $request->validate([
                    'date' => 'required',
                    'daily_sales' => 'required',
                ]);

                $daily_sale = daily_sales::find($request->id);

                $daily_sale->date = $validatedData['date'];
                $daily_sale->daily_sales = $validatedData['daily_sales'];
                $daily_sale->save();


                return response()->json(
                    [
                        "dailySale" => $daily_sale
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "日次売上の更新に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $daily_sale = daily_sales::find($request->id);
                if (!$daily_sale) {
                    return response()->json([
                        'message' =>
                        '日次売上が見つかりません！
                        もう一度お試しください！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
                $daily_sale->delete();
                return response()->json([
                    "deleteId" => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                '日次売上の削除に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
