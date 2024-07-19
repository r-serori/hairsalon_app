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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class MonthlySalesController extends Controller
{
    public function index()
    {
        try {
            $user = User::find(Auth::id());

            if ($user && $user->hasRole(Roles::$OWNER)) {

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $currentYear = Carbon::now()->year;

                $monthlySalesCacheKey = 'owner_' . $ownerId . 'monthlySales';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                // 月別売上一覧を取得
                $monthly_sales = Cache::remember($monthlySalesCacheKey, $expirationInSeconds, function () use ($ownerId, $currentYear) {
                    $currentYearStart = Carbon::create($currentYear, 1, 1)->format('Y-m');
                    $currentYearEnd = Carbon::create($currentYear, 12, 31)->format('Y-m');
                    return MonthlySale::where('owner_id', $ownerId)->whereBetween('year_month', [$currentYearStart, $currentYearEnd])->oldest('year_month')->get();
                });

                if ($monthly_sales->isEmpty()) {
                    return response()->json([
                        "message" =>
                        "初めまして！予約表画面の月次売上更新ボタンから月次売上を作成しましょう！",
                        'monthlySales' => $monthly_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'message' => $currentYear . '年の月次売上データを取得しました！',
                        'monthlySales' => $monthly_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                '月次売上が見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function selectedMonthlySales($year)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $ownerId = Owner::where('user_id', $user->id)->value('id');
                $decodedYear = urldecode($year);

                $decodedYearStart = Carbon::create($decodedYear, 1, 1)->format('Y-m');
                $decodedYearEnd = Carbon::create($decodedYear, 12, 31)->format('Y-m');
                $monthly_sales = MonthlySale::where('owner_id', $ownerId)->whereBetween('year_month', [$decodedYearStart, $decodedYearEnd])->oldest('year_month')->get();

                if ($monthly_sales->isEmpty()) {
                    return response()->json([
                        "message" => "選択した売上データがありません！予約表画面の月次売上更新ボタンから月次売上を作成しましょう！",
                        'monthlySales' => $monthly_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'message' => $decodedYear . '年の月次売上データを取得しました！',
                        'monthlySales' => $monthly_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
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
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                // バリデーションルールを定義する
                $validator = Validator::make($request->all(), [

                    'year_month' => 'required|date_format:Y-m',
                    'monthly_sales' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        "message" => "月次売上の作成に失敗しました！入力内容を確認してください！",
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validatedData = $validator->validated();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $existMonthlySale = MonthlySale::where('owner_id', $ownerId)->where('year_month', $validatedData['year_month'])->first();

                if ($existMonthlySale) {
                    return response()->json([
                        "message" => "その月次売上は既に存在しています！月次売上画面から編集をして数値を変更するか、削除してもう一度この画面から更新してください！"
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                // 月別売上モデルを作成して保存する
                $monthly_sales = MonthlySale::create([
                    'year_month' => $validatedData['year_month'],
                    'monthly_sales' => $validatedData['monthly_sales'],
                    'owner_id' => $ownerId
                ]);

                $monthlySalesCacheKey = 'owner_' . $ownerId . 'monthlySales';

                Cache::forget($monthlySalesCacheKey);

                DB::commit();
                // 成功したらリダイレクト
                return response()->json([
                    "monthlySale" => $monthly_sales,
                    "message" => "月次売上を作成しました！",
                    "status" => "success"
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => "月次売上の作成に失敗しました！
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
                // バリデーションルールを定義する
                $validator = Validator::make($request->all(), [
                    'id' => 'required|integer',
                    'year_month' => 'required|date_format:Y-m',
                    'monthly_sales' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        "message" => "月次売上の更新に失敗しました！入力内容を確認してください！",
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validatedData = $validator->validated();

                // 月別売上を取得する
                $monthly_sale = MonthlySale::find($validatedData['id']);

                // 月別売上を更新する
                $monthly_sale->year_month = $validatedData['year_month'];
                $monthly_sale->monthly_sales = $validatedData['monthly_sales'];
                $monthly_sale->save();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $monthlySalesCacheKey = 'owner_' . $ownerId . 'monthlySales';

                Cache::forget($monthlySalesCacheKey);

                DB::commit();
                // 成功したらリダイレクト
                return response()->json(
                    [
                        "monthlySale" => $monthly_sale,
                        "message" => "月次売上を更新しました！",
                        "status" => "success"
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
                "message" => "月次売上の更新に失敗しました！
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
                $monthly_sale = MonthlySale::find($request->id);
                if (!$monthly_sale) {
                    return response()->json([
                        'message' =>
                        '月次売上が見つかりません！
                        もう一度お試しください！'
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $monthly_sale->delete();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $monthlySalesCacheKey = 'owner_' . $ownerId . 'monthlySales';

                Cache::forget($monthlySalesCacheKey);

                DB::commit();
                return response()->json([
                    "deleteId" => $request->id,
                    "message" => "月次売上を削除しました！",
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
                '月次売上が見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
