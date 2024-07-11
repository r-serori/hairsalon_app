<?php

namespace App\Http\Controllers;

use App\Models\DailySale;
use Illuminate\Http\Request;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DailySalesController extends Controller
{

    public function index()
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $currentYear = Carbon::now()->year;

                $dailySalesCacheKey = 'owner_' . $ownerId . 'dailySales';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $daily_sales = Cache::remember($dailySalesCacheKey, $expirationInSeconds, function () use ($ownerId, $currentYear) {
                    return DailySale::whereYear('date', $currentYear)
                        ->where('owner_id',  $ownerId)->get();
                });

                if ($daily_sales->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！予約表画面の日次売上作成ボタンから日次売上を作成しましょう！",
                        'dailySales' => $daily_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'massage' => $currentYear . '年の日次売上データです！',
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
                    '日次売上が見つかりません！もう一度お試しください！'
                ],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            )->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function selectedDailySales($year)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {

                $decodedYear = urldecode($year);

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $dailySalesCacheKey = 'owner_' . $ownerId . 'dailySales';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $daily_sales = Cache::remember($dailySalesCacheKey, $expirationInSeconds, function () use ($ownerId, $decodedYear) {
                    return DailySale::whereYear('date', $decodedYear)->where('owner_id',  $ownerId)->get();
                });
                if ($daily_sales->isEmpty()) {
                    return response()->json([
                        "message" => "選択した売上データがありません！予約表画面の日次売上作成ボタンから日次売上を作成しましょう！",
                        'dailySales' => $daily_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'massage' => $decodedYear . '年の日次売上データです！',
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
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $validatedData
                    = $request->validate([
                        'date' => 'required|string',
                        'daily_sales' => 'required|integer',
                    ]);

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $existDailySale = DailySale::where('date', $validatedData['date'])->where('owner_id', $ownerId)->first();

                if ($existDailySale) {
                    return response()->json([
                        "message" => "その日の日次売上は既に存在しています！"
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $daily_sales =
                    DailySale::create([
                        'date' => $validatedData['date'],
                        'daily_sales' => $validatedData['daily_sales'],
                        'owner_id' => $ownerId
                    ]);


                $dailySalesCacheKey = 'owner_' . $ownerId . 'dailySales';

                Cache::forget($dailySalesCacheKey);

                DB::commit();


                return response()->json([
                    "dailySale" => $daily_sales
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return response()->json([
                "message" => "日次売上の作成に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    // public function show($id)
    // {
    //     try {
    //         if (Gate::allows(Permissions::$OWNER_PERMISSION)) {
    //             $daily_sale = DailySale::find($id);

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
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $validatedData = $request->validate([
                    'date' => 'required',
                    'daily_sales' => 'required',
                ]);

                $daily_sale = DailySale::find($request->id);

                $daily_sale->date = $validatedData['date'];
                $daily_sale->daily_sales = $validatedData['daily_sales'];
                $daily_sale->save();
                $ownerId = Owner::where('user_id', $user->id)->value('id');


                $dailySalesCacheKey = 'owner_' . $ownerId . 'dailySales';

                Cache::forget($dailySalesCacheKey);

                DB::commit();

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
            DB::rollBack();
            return response()->json([
                "message" => "日次売上の更新に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $daily_sale = DailySale::find($request->id);
                if (!$daily_sale) {
                    return response()->json([
                        'message' =>
                        '日次売上が見つかりません！
                        もう一度お試しください！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
                $daily_sale->delete();



                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $dailySalesCacheKey = 'owner_' . $ownerId . 'dailySales';

                Cache::forget($dailySalesCacheKey);

                DB::commit();
                return response()->json([
                    "deleteId" => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' =>
                '日次売上の削除に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
