<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonthlySale;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use App\Models\Staff;


class MonthlySalesController extends Controller
{
    public function index()
    {
        try {
            $user = User::find(Auth::id());

            if ($user && $user->hasRole(Roles::$OWNER)) {

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $monthlySalesCacheKey = 'owner_' . $ownerId . 'monthlySales';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                // 月別売上一覧を取得
                $monthly_sales = Cache::remember($monthlySalesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return MonthlySale::where('owner_id', $ownerId)->get();
                });

                if ($monthly_sales->isEmpty()) {
                    return response()->json([
                        "message" =>
                        "初めまして！予約表画面の月次売上更新ボタンから月次売上を作成しましょう！",
                        'monthlySales' => $monthly_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'monthlySales' => $monthly_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                '月次売上が見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                // バリデーションルールを定義する
                $validatedData = $request->validate([
                    'year_month' => 'required|string',
                    'monthly_sales' => 'required|integer',
                ]);

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                // 月別売上モデルを作成して保存する
                $monthly_sales = MonthlySale::create([
                    'year_month' => $validatedData['year_month'],
                    'monthly_sales' => $validatedData['monthly_sales'],
                    'owner_id' => $ownerId
                ]);

                $monthlySalesCacheKey = 'owner_' . $ownerId . 'monthlySales';

                Cache::forget($monthlySalesCacheKey);
                // 成功したらリダイレクト
                return response()->json([
                    "monthlySale" => $monthly_sales
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "月次売上の作成に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    // public function show($id)
    // {
    //     try {
    //         if (Gate::allows(Permissions::$OWNER_PERMISSION)) {
    //             // 指定されたIDの月別売上を取得
    //             $monthly_sale = MonthlySale::find($id);

    //             // 月別売上を表示
    //             return response()->json([
    //                 'monthlySale' => $monthly_sale
    //             ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         } else {
    //             return response()->json([
    //                 "message" => "あなたには権限がありません！"
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' =>
    //             '月次売上が見つかりません！'
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }



    public function update(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                // バリデーションルールを定義する
                $validatedData = $request->validate([
                    'year_month' => 'required|string',
                    'monthly_sales' => 'required|integer',
                ]);

                // 月別売上を取得する
                $monthly_sale = MonthlySale::find($request->id);

                // 月別売上を更新する
                $monthly_sale->year = $validatedData['year_month'];
                $monthly_sale->monthly_sales = $validatedData['monthly_sales'];
                $monthly_sale->save();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $monthlySalesCacheKey = 'owner_' . $ownerId . 'monthlySales';

                Cache::forget($monthlySalesCacheKey);
                // 成功したらリダイレクト
                return response()->json(
                    [
                        "monthlySale" => $monthly_sale
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
                "message" => "月次売上の更新に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $monthly_sale = MonthlySale::find($request->id);
                if (!$monthly_sale) {
                    return response()->json([
                        'message' =>
                        '月次売上が見つかりません！
                        もう一度お試しください！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $monthly_sale->delete();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $monthlySalesCacheKey = 'owner_' . $ownerId . 'monthlySales';

                Cache::forget($monthlySalesCacheKey);
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
                '月次売上が見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
