<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\owner;
use App\Models\staff;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;


class UserPostController extends Controller
{



    public function ownerStore(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'store_name' => ['required', 'string', 'max:50'],
                'address' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'max:13'],
                'user_id' => ['required', 'integer',],
            ]);

            $user = User::where('id', $request->user_id)->first();

            if (!empty($user)) {
                $owner = owner::create([
                    'store_name' => $request->store_name,
                    'address' => $request->address,
                    'phone_number' => $request->phone_number,
                    'user_id' => $request->user_id,
                ]);

                $responseOwner = [
                    'id' => $owner->id,
                    'store_name' => $owner->store_name,
                    'address' => $request->address,
                    'phone_number' => $request->phone_number,
                    'user_id' => $request->user_id,
                ];

                return response()->json(
                    [
                        'message' => 'オーナー用ユーザー登録に成功しました!',
                        'responseOwner' => $responseOwner,
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json(
                    [
                        "message" => "もう一度最初からやり直してください！",
                    ],
                    404,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    "message" => "オーナー用ユーザー登録に失敗しました！",
                ],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            )->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function staffStore(Request $request): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {

                $request->validate([
                    'name' => ['required', 'string', 'max:50'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'phone_number' => ['required', 'string', 'max:13'],
                    'password' => [
                        'required',
                    ],
                    'role' => ['required', 'string', 'max:10'],
                    'isAttendance' => ['required', 'boolean'],
                    'owner_id' => ['required', 'integer', 'exists:owners,id'],
                ]);

                $userID = User::where('email', $request->email)->first();


                if ($userID) {
                    return
                        response()->json([
                            'message' => 'メールアドレスが既に存在しています！
                            他のメールアドレスを入力してください！',
                        ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone_number' => $request->phone_number,
                        'password' => Hash::make($request->password),
                        'role' => $request->role === 'マネージャー' ? Roles::MANAGER : Roles::STAFF,
                        'isAttendance' => $request->isAttendance,
                    ]);

                    // event(new Registered($user));

                    // Auth::login($user);

                    $staff = staff::create([
                        'user_id' => $user->id,
                        'owner_id' => $request->owner_id,
                    ]);

                    $responseUser = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                        'role' => $user->role,
                        'isAttendance' => $user->isAttendance,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ];
                    return response()->json(
                        [
                            'message' => 'スタッフ用ユーザー登録に成功しました!',
                            'responseUser' => $responseUser,
                            'responseStaff' => $staff,
                        ],
                        200,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            // Log::error('ユーザー登録処理中にエラーが発生しました。');
            // Log::error('エラー内容: ' . $e);
            return response()->json([
                'message' => 'ユーザー登録に失敗しました！もう一度最初からやり直してください！',
            ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function updatePermission(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {

                $request->validate([
                    'id' => ['required', 'integer', 'exists:users,id'],
                    'role' => ['required', 'string', 'max:10'],
                ]);

                $user = User::where('id', $request->id)->first();

                if (!empty($user)) {
                    $user->role = $request->role;
                    $user->save();

                    return response()->json([
                        'message' => '権限の変更に成功しました！',
                        'responseUser' => $user->only(['id', 'name', 'email', 'phone_number', 'role', 'isAttendance', 'created_at', 'updated_at']),
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'message' => 'スタッフ情報が見つかりませんでした！',
                    ], 404, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'エラーが発生しました！もう一度やり直してください！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
