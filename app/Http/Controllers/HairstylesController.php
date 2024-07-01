<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hairstyle;

use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Cache;

class HairstylesController extends Controller
{

    public function index($id): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                $user_id = urldecode($id);
                $hairstylesCacheKey = 'owner_' . $user_id . 'hairstyles';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $hairstyles = Cache::remember($hairstylesCacheKey, $expirationInSeconds, function () use ($user_id) {
                    return Hairstyle::where('owner_id', $user_id)->get();
                });

                if ($hairstyles->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！新規作成ボタンから使用するヘアスタイルを作成しましょう！",
                        'hairstyles' => $hairstyles
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'hairstyles' => $hairstyles
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => "ヘアスタイルが見つかりませんでした！
                もう一度お試しください！"
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $validatedData = $request->validate([
                    'hairstyle_name' => 'required|string',
                ]);

                $hairstyle = Hairstyle::create([
                    'hairstyle_name' => $validatedData['hairstyle_name'],
                ]);
                $hairstylesCacheKey = 'owner_' . $request->owner_id . 'hairstyles';

                Cache::forget($hairstylesCacheKey);
                return response()->json([
                    "hairstyle" => $hairstyle
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "ヘアスタイルの作成に失敗しました！
                もう一度お試しください！"
            ], 500);
        }
    }

    // public function show($id)
    // {
    //     try {

    //         $hairstyle = Hairstyle::find($id);
    //         if (!$hairstyle) {
    //             return response()->json([
    //                 'message' => 'ヘアスタイルが見つかりません！'
    //             ], 404);
    //         }

    //         return response()->json([
    //             'hairstyle' => $hairstyle
    //         ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'ヘアスタイルが見つかりません！'
    //         ], 500);
    //     }
    // }



    public function update(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $validatedData = $request->validate([
                    'hairstyle_name' => 'required|string',
                ]);

                $hairstyle = Hairstyle::find($request->id);
                $hairstyle->hairstyle_name = $validatedData['hairstyle_name'];

                $hairstyle->save();
                $hairstylesCacheKey = 'owner_' . $request->owner_id . 'hairstyles';

                Cache::forget($hairstylesCacheKey);
                return response()->json(
                    [
                        "hairstyle" => $hairstyle
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
                "message" => "ヘアスタイルの更新に失敗しました！
                もう一度お試しください！"
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {
                $hairstyle = Hairstyle::find($request->id);
                if (!$hairstyle) {
                    return response()->json([
                        'message' => 'ヘアスタイルが見つかりません！
                        もう一度お試しください！'
                    ], 500);
                }
                $hairstyle->delete();
                $hairstylesCacheKey = 'owner_' . $request->owner_id . 'hairstyles';

                Cache::forget($hairstylesCacheKey);
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
                'message' => 'ヘアスタイルの削除に失敗しました！
                もう一度お試しください！'
            ], 500);
        }
    }
}
