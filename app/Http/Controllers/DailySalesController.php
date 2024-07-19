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
use Illuminate\Support\Facades\Validator;

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

                    $currentYearStart = Carbon::create($currentYear, 1, 1);
                    $currentYearEnd = Carbon::create($currentYear, 12, 31); // 次の年の最終日

                    return DailySale::where('owner_id', $ownerId)
                        ->whereBetween('date', [$currentYearStart, $currentYearEnd])->oldest('date')->get();
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
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
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

                $decodeYearStart = Carbon::create($decodedYear, 1, 1);
                $decodeYearEnd = Carbon::create($decodedYear, 12, 31); // 次の年の最終日

                $daily_sales = DailySale::where('owner_id', $ownerId)
                    ->whereBetween('date', [$decodeYearStart, $decodeYearEnd])->oldest('date')
                    ->get();

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
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
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
                $validator = Validator::make($request->all(), [
                    'date' => 'required|date_format:Y-m-d',
                    'daily_sales' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        "message" => "日次売上の作成に失敗しました！
                        もう一度お試しください！"
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validatedData = $validator->validate();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $existDailySale = DailySale::whereDate('date', $validatedData['date'])->where('owner_id', $ownerId)->first();

                if ($existDailySale) {
                    return response()->json([
                        "message" => "その日の日次売上は既に存在しています！日次売上画面から編集をして数値を変更するか、削除してもう一度この画面から更新してください！"
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
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
                    "dailySale" => $daily_sales,
                    "message" => "日次売上を作成しました！",
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
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

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $validator = Validator::make($request->all(), [
                    'date' => 'required | date_format:Y-m-d',
                    'daily_sales' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        "message" => "入力内容を確認してください！"
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validatedData = $validator->validate();

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
                        "dailySale" => $daily_sale,
                        "message" => "日次売上を更新しました！",
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
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
                        '日次売上が見つかりません！もう一度お試しください！'
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
                $daily_sale->delete();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $dailySalesCacheKey = 'owner_' . $ownerId . 'dailySales';

                Cache::forget($dailySalesCacheKey);

                DB::commit();
                return response()->json([
                    "deleteId" => $request->id,
                    'message' => "日次売上を削除しました！"
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
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
