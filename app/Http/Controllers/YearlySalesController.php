<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YearlySale;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use App\Models\Staff;

class YearlySalesController extends Controller
{
    public function index()
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {




                $ownerId = Owner::where('user_id', $user->id)->first()->value('id');


                $yearlySalesCacheKey = 'owner_' . $ownerId . 'yearlySales';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $yearly_sales = Cache::remember($yearlySalesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return YearlySale::where('owner_id', $ownerId)->get();
                });
                if ($yearly_sales->isEmpty()) {
                    return response()->json([
                        "message" =>
                        "初めまして！予約表画面の年次売上作成ボタンから年次売上を作成しましょう！",
                        'yearlySales' => $yearly_sales
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'yearlySales' => $yearly_sales
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
                '年次売上が見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $validatedData = $request->validate([
                    'year' => 'required|string',
                    'yearly_sales' => 'required|integer',
                ]);

                $ownerId = Owner::where('user_id', $user->id)->first()->value('id');
                $yearly_sale = YearlySale::create([
                    'year' => $validatedData['year'],
                    'yearly_sales' => $validatedData['yearly_sales'],
                    'owner_id' => $ownerId
                ]);
                $yearlySalesCacheKey = 'owner_' . $ownerId . 'yearlySales';

                Cache::forget($yearlySalesCacheKey);

                return response()->json([
                    "yearlySale" => $yearly_sale
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "年次売上の作成に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    // public function show($id)
    // {
    //     try {
    //         if (Gate::allows(Permissions::$OWNER_PERMISSION)) {
    //             $yearly_sale = YearlySale::find($id);

    //             return response()->json([
    //                 'yearlySale' => $yearly_sale
    //             ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         } else {
    //             return response()->json([
    //                 "message" => "あなたには権限がありません！"
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' =>
    //             '年次売上が見つかりません！'
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }



    public function update(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $validatedData = $request->validate([
                    'year' => 'required|string',
                    'yearly_sales' => 'required|integer',
                ]);

                $yearly_sale = YearlySale::find($request->id);

                $yearly_sale->year = $validatedData['year'];
                $yearly_sale->yearly_sales = $validatedData['yearly_sales'];
                $yearly_sale->save();
                $ownerId = Owner::where('user_id', $user->id)->first()->value('id');
                $yearlySalesCacheKey = 'owner_' . $ownerId . 'yearlySales';

                Cache::forget($yearlySalesCacheKey);

                return response()->json(
                    [
                        "yearlySale" => $yearly_sale
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
                'message' =>
                '年次売上が見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $yearly_sale = YearlySale::find($request->id);
                if (!$yearly_sale) {
                    return response()->json([
                        'message' =>
                        '年次売上が見つかりません！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $yearly_sale->delete();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $yearlySalesCacheKey = 'owner_' . $ownerId . 'yearlySales';

                Cache::forget($yearlySalesCacheKey);
                return response()->json([
                    "deleteId" => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！
                    "
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                '年次売上が見つかりません！
            もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
