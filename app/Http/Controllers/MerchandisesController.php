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
use App\Models\Owner;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MerchandisesController extends Controller
{

    public function index()
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

                $merchandisesCacheKey = 'owner_' . $ownerId . 'merchandises';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $merchandises = Cache::remember($merchandisesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return Merchandise::where('owner_id', $ownerId)->get();
                });

                if ($merchandises->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！新規作成ボタンから物販商品を作成しましょう！",
                        'merchandises' => $merchandises
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'merchandises' => $merchandises,
                        'message' => "物販商品の取得に成功しました！"
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
                '物販商品が見つかりません！もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $validator = Validator::make($request->all(), [
                    'merchandise_name' => 'required|string|max:255',
                    'price' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => '入力内容が正しくありません！'
                    ], 400);
                }

                $validatedData = $validator->validate();

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $merchandise = Merchandise::create([
                    'merchandise_name' => $validatedData['merchandise_name'],
                    'price' => $validatedData['price'],
                    'owner_id' => $ownerId

                ]);
                $merchandisesCacheKey = 'owner_' . $ownerId . 'merchandises';

                Cache::forget($merchandisesCacheKey);

                DB::commit();
                return response()->json([
                    "merchandise" => $merchandise

                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => "物販商品の作成に失敗しました！
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

                    'merchandise_name' => 'required',
                    'price' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => '入力内容が正しくありません！'
                    ], 400);
                }

                $validatedData = $validator->validate();

                $merchandise = Merchandise::find($request->id);


                $merchandise->merchandise_name = $validatedData['merchandise_name'];
                $merchandise->price = $validatedData['price'];

                $merchandise->save();

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $merchandisesCacheKey = 'owner_' . $ownerId . 'merchandises';

                Cache::forget($merchandisesCacheKey);

                DB::commit();

                return response()->json(
                    [
                        "merchandise" => $merchandise,
                        "message" => "物販商品の更新に成功しました！"
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
                "message" => "物販商品の更新に失敗しました！
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
                $merchandise = Merchandise::find($request->id);
                if (!$merchandise) {
                    return response()->json([
                        'message' =>
                        '物販商品が見つかりません！もう一度お試しください！'
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $merchandise->delete();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $merchandisesCacheKey = 'owner_' . $ownerId . 'merchandises';

                Cache::forget($merchandisesCacheKey);

                DB::commit();
                return response()->json([
                    "deleteId" => $request->id
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
                '物販商品の削除に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
