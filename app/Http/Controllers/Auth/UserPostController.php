<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\owner;
use App\Models\staff;

class UserPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }


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
                        'resStatus' => "success",
                        'message' => 'オーナー用ユーザー登録に成功しました!',
                        'responseOwner' => $responseOwner,
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'resStatus' => "error",
                        "message" => "もう一度最初からやり直してください。",
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            return response()->json(
                [
                    'resStatus' => "error",
                    "message" => $e->getMessage(),
                ],
                500
            );
        }
    }

    public function secondStore(Request $request): JsonResponse
    {
        try {


            $request->validate([
                'name' => ['required', 'string', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'phone_number' => ['required', 'string', 'max:13'],
                'password' => [
                    'required',
                ],
                'role' => ['required', 'string', 'max:10'],
                'isAttendance' => ['required', 'boolean'],
            ]);

            $userID = User::where('email', $request->email)->first();


            if ($userID) {
                return
                    response()->json([
                        "resStatus" => 'error',
                        'message' => 'メールアドレスが既に存在しています。',
                    ], 400);
            } else {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'isAttendance' => $request->isAttendance,
                ]);

                // event(new Registered($user));

                // Auth::login($user);

                $staff = staff::create([
                    'position' => $request->role,
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
                        'resStatus' => "success",
                        'message' => 'スタッフ用ユーザー登録に成功しました!',
                        'responseUser' => $responseUser,
                        'responseStaff' => $staff,
                    ],
                    200
                );
            }
        } catch (\Exception $e) {
            Log::error('ユーザー登録処理中にエラーが発生しました。');
            Log::error('エラー内容: ' . $e);
            return response()->json([
                "resStatus" => 'error',
                'message' => 'ユーザー登録に失敗しました。',
            ], 400);
        }
    }
}
