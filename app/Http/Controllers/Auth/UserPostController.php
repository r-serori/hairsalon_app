<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Owner;
use App\Models\Staff;
use App\Enums\Roles;
use Illuminate\Http\JsonResponse as HttpJsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserPostController extends Controller
{



    public function ownerStore(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'store_name' => 'required | string | max:100',
                'postal_code' => 'required | integer',
                'prefecture' => 'required | string | max:100',
                'city' => 'required | string | max:100',
                'addressLine1' => 'required | string | max:200',
                'addressLine2' => 'nullable | string | max:200',
                'phone_number' => 'required | string | max:20',
                'user_id' => 'required | integer | exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    ['message' => '入力内容が正しくありません。'],
                    400,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            }

            $validateData = (object)$validator->validate();

            $user = User::where('id', $validateData->user_id)->first();

            if (!empty($user)) {
                $owner = Owner::create([
                    'store_name' => $validateData->store_name,
                    'postal_code' => $validateData->postal_code,
                    'prefecture' => $validateData->prefecture,
                    'city' => $validateData->city,
                    'addressLine1' => $validateData->addressLine1,
                    'addressLine2' => $validateData->addressLine2 ? $validateData->addressLine2 : '',
                    'phone_number' => $validateData->phone_number,
                    'user_id' => $validateData->user_id,
                ]);

                DB::commit();

                return response()->json(
                    [
                        'message' => 'オーナー用ユーザー登録に成功しました!',
                        'owner' => $owner,
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {

                DB::rollBack();
                return response()->json(
                    [
                        "message" => "オーナー用ユーザー登録に失敗しました！もう一度最初からやり直してください！",
                    ],
                    400,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json(
                [
                    "message" => "オーナー用ユーザー登録に失敗しました！もう一度やり直してください！",
                ],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            )->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function staffStore(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {

                $owner = Owner::where('user_id', $user->id)->first();

                if (empty($owner)) {
                    throw new \Exception('オーナー情報が見つかりませんでした！');
                }

                $validator = Validator::make($request->all(), [

                    'name' => 'required|string|max:50',
                    'email' => ' required|string|email|max:200|unique:users',
                    'phone_number' => 'required|string|max:20',
                    'password' =>  $this->passwordRules(),
                    'role' => 'required|string|max:30',
                    'isAttendance' => 'required|boolean',
                    'user_id' => 'required|integer|exists:users,id',
                ]);

                if ($validator->fails()) {
                    return response()->json(
                        ['message' => '入力内容が正しくありません。'],
                        400,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validateData = (object)$validator->validate();

                $userId = User::where('email', $validateData->email)->first();


                if ($userId) {
                    return
                        response()->json([
                            'message' => 'メールアドレスが既に存在しています！
                            他のメールアドレスを入力してください！',
                        ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    $user = User::create([
                        'name' => $validateData->name,
                        'email' => $validateData->email,
                        'phone_number' => $validateData->phone_number,
                        'password' => Hash::make($validateData->password),
                        'role' => $validateData->role === 'マネージャー' ? Roles::$MANAGER : Roles::$STAFF,
                        'isAttendance' => $validateData->isAttendance,
                        'user_id' => $owner->id,
                    ]);

                    // event(new Registered($user));

                    // Auth::login($user);

                    $staff = staff::create([
                        'user_id' => $user->id,
                        'owner_id' => $owner->id,
                    ]);

                    $responseUser = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                        'role' => $user->role === Roles::$MANAGER ? 'マネージャー' : 'スタッフ',
                        'isAttendance' => $user->isAttendance,
                    ];

                    DB::commit();
                    return response()->json(
                        [
                            'message' => 'スタッフ用ユーザー登録に成功しました!',
                            'responseUser' => $responseUser,
                        ],
                        200,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('ユーザー登録処理中にエラーが発生しました。');
            // Log::error('エラー内容: ' . $e);
            return response()->json([
                'message' => 'ユーザー登録に失敗しました！もう一度最初からやり直してください！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function ownerUpdate(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {

                $owner = Owner::where('user_id', $user->id)->first();

                if (empty($owner)) {
                    throw new \Exception('オーナー情報が見つかりませんでした！');
                }

                $validator = Validator::make($request->all(), [

                    'store_name' => 'required|string|max:100',
                    'postal_code' => 'required|integer',
                    'prefecture' => 'required|string|max:100',
                    'city' => 'required|string|max:100',
                    'addressLine1' => 'required|string|max:200',
                    'addressLine2' => 'nullable|string|max:200',
                    'phone_number' => 'required|string|max:20',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => '入力内容が正しくありません。',
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validateData = (object)$validator->validate();

                $owner->store_name = $validateData->store_name;
                $owner->postal_code = $validateData->postal_code;
                $owner->prefecture = $validateData->prefecture;
                $owner->city = $validateData->city;
                $owner->addressLine1 = $validateData->addressLine1;
                $owner->addressLine2 = $validateData->addressLine2 ? $validateData->addressLine2 : '';
                $owner->phone_number = $validateData->phone_number;
                $owner->save();

                DB::commit();

                return response()->json([
                    'message' => 'オーナー情報の更新に成功しました！',
                    'owner' => $owner,
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'エラーが発生しました！もう一度やり直してください！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function updatePermission(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {

                $owner = Owner::find(Auth::id());

                if (empty($owner)) {
                    return response()->json([
                            'message' => 'オーナー情報が見つかりませんでした！',
                        ], 404, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validator = Validator::make($request->all(), [
                    'id' => 'required|integer|exists:users,id',
                    'role' => 'required|string|max:30',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => '入力内容が正しくありません！',
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validateData = (object)$validator->validate();

                $updateUser = User::where('id', $validateData->id)->first();

                if (!empty($user)) {
                    $updateUser->role = $validateData->role === 'マネージャー' ? Roles::$MANAGER : Roles::$STAFF;
                    $updateUser->save();

                    $responseUser = [
                        'id' => $updateUser->id,
                        'name' => $updateUser->name,
                        'phone_number' => $updateUser->phone_number,
                        'role' => $updateUser->role === Roles::$MANAGER ? 'マネージャー' : 'スタッフ',
                        'isAttendance' => $updateUser->isAttendance,
                    ];

                    DB::commit();

                    return response()->json([
                        'message' => '権限の変更に成功しました！',
                        'responseUser' => $responseUser,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'スタッフ情報が見つかりませんでした！',
                    ], 404, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'エラーが発生しました！もう一度やり直してください！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
