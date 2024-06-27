<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchandise;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Cache;

class MerchandisesController extends Controller
{

    public function index($id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $user_id = urldecode($id);
                $merchandisesCacheKey = 'owner_' . $user_id . 'merchandises';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $merchandises = Cache::remember($merchandisesCacheKey, $expirationInSeconds, function () use ($user_id) {
                    return Merchandise::where('owner_id', $user_id)->get();
                });

                if ($merchandises->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！新規作成ボタンから物販商品を作成しましょう！",
                        'merchandises' => $merchandises
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'merchandises' => $merchandises
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                '物販商品が見つかりません！
                もう一度お試しください！'
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $validatedData = $request->validate([
                    'merchandise_name' => 'required|string|max:255',
                    'price' => 'required|integer',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                $merchandise = Merchandise::create([
                    'merchandise_name' => $validatedData['merchandise_name'],
                    'price' => $validatedData['price'],
                    'owner_id' => $validatedData['owner_id'],

                ]);
                $merchandisesCacheKey = 'owner_' . $request->owner_id . 'merchandises';

                Cache::forget($merchandisesCacheKey);
                return response()->json([
                    "merchandise" => $merchandise

                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "物販商品の作成に失敗しました！
                もう一度お試しください！"
            ], 500);
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $merchandise = Merchandise::find($id);

    //         return response()->json([
    //             'merchandise' => $merchandise
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' =>
    //             '物販商品が見つかりません！'
    //         ], 500);
    //     }
    // }



    public function update(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {

                $validatedData = $request->validate([
                    'merchandise_name' => 'required',
                    'price' => 'required',
                ]);

                $merchandise = Merchandise::find($request->id);


                $merchandise->merchandise_name = $validatedData['merchandise_name'];
                $merchandise->price = $validatedData['price'];

                $merchandise->save();
                $merchandisesCacheKey = 'owner_' . $request->owner_id . 'merchandises';

                Cache::forget($merchandisesCacheKey);

                return response()->json(
                    [
                        "merchandise" => $merchandise,
                    ],
                    200
                );
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "物販商品の更新に失敗しました！
                もう一度お試しください！"
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $merchandise = Merchandise::find($request->id);
                if (!$merchandise) {
                    return response()->json([
                        'message' =>
                        '物販商品が見つかりません！
                        もう一度お試しください！'
                    ], 500);
                }

                $merchandise->delete();
                $merchandisesCacheKey = 'owner_' . $request->owner_id . 'merchandises';

                Cache::forget($merchandisesCacheKey);
                return response()->json([
                    "deleteId" => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                '物販商品の削除に失敗しました！
                もう一度お試しください！'
            ], 500);
        }
    }
}
