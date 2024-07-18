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
use App\Models\Owner;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HairstylesController extends Controller
{

    public function index(): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $hairstylesCacheKey = 'owner_' . $ownerId . 'hairstyles';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $hairstyles = Cache::remember($hairstylesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return Hairstyle::where('owner_id', $ownerId)->get();
                });

                if ($hairstyles->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！新規作成ボタンから使用するヘアスタイルを作成しましょう！",
                        'hairstyles' => $hairstyles
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'hairstyles' => $hairstyles,
                        'message' => "ヘアスタイルの取得に成功しました！"
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => "ヘアスタイルが見つかりませんでした！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $validator = Validator::make($request->all(), [

                    'hairstyle_name' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => '入力内容をご確認ください！'
                    ], 400);
                }

                $validatedData = $validator->validate();

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $hairstyle = Hairstyle::create([
                    'hairstyle_name' => $validatedData['hairstyle_name'],
                    'owner_id' => $ownerId
                ]);

                $hairstylesCacheKey = 'owner_' . $ownerId . 'hairstyles';

                Cache::forget($hairstylesCacheKey);

                DB::commit();
                return response()->json([
                    "hairstyle" => $hairstyle,
                    "message" => "ヘアスタイルの作成に成功しました！"
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => "ヘアスタイルの作成に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }




    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $validator = Validator::make($request->all(), [

                    'hairstyle_name' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => '入力内容をご確認ください！'
                    ], 400);
                }

                $validatedData = $validator->validate();

                $hairstyle = Hairstyle::find($request->id);
                $hairstyle->hairstyle_name = $validatedData['hairstyle_name'];

                $hairstyle->save();

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }
                $hairstylesCacheKey = 'owner_' . $ownerId . 'hairstyles';

                Cache::forget($hairstylesCacheKey);

                DB::commit();
                return response()->json(
                    [
                        "hairstyle" => $hairstyle,
                        "message" => "ヘアスタイルの更新に成功しました！"
                    ],
                    200
                );
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => "ヘアスタイルの更新に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {
                $hairstyle = Hairstyle::find($request->id);
                if (!$hairstyle) {
                    return response()->json([
                        'message' => 'ヘアスタイルが見つかりません！もう一度お試しください！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
                $hairstyle->delete();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $hairstylesCacheKey = 'owner_' . $ownerId . 'hairstyles';

                Cache::forget($hairstylesCacheKey);

                DB::commit();
                return response()->json([
                    "deleteId" => $request->id,
                    "message" => "ヘアスタイルの削除に成功しました！"
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'ヘアスタイルの削除に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
