<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\User;
use App\Models\Staff;

use App\Enums\Roles;
use App\Models\AttendanceTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Owner;

class UserGetController extends Controller
{
    public function getUsers(): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                $owner = Owner::where('user_id', $user->id)->first();

                $staffs = Staff::where('owner_id', $owner->id)->get();



                if ($staffs->isEmpty()) {
                    $responseUsers = $user->only([
                        'id',
                        'name',
                        'phone_number',
                        'role' => $user->role === Roles::$OWNER ? 'オーナー' : 'マネージャー',
                        'isAttendance',
                    ]);
                    return response()->json([
                        'message' => 'スタッフ情報がありません!登録してください！',
                        'responseUsers' => $responseUsers,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {

                    $userIds = $staffs->pluck('user_id');

                    $userIds->push($user->id);

                    $users = User::whereIn('id', $userIds)->get();

                    $responseUsers = $users->map(function ($resUser) {
                        return [
                            'id' => $resUser->id,
                            'name' => $resUser->name,
                            'phone_number' => $resUser->phone_number,
                            'role' => $resUser->role === Roles::$OWNER ? 'オーナー' : ($resUser->role === Roles::$MANAGER ? 'マネージャー' : 'スタッフ'),
                            'isAttendance' => $resUser->isAttendance,
                        ];
                    });

                    $userCount = count($users);

                    return response()->json([
                        'message' => 'ユーザー情報を取得しました!',
                        'responseUsers' => $responseUsers,
                        'userCount' => $userCount,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'エラーが発生しました。もう一度やり直してください！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function show(): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                if (!empty($user)) {

                    $responseUser = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                        'isAttendance' => $user->isAttendance,
                    ];

                    return response()->json([
                        'message' => 'ユーザー情報を取得しました!',
                        'responseUser' => $responseUser,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'message' => 'ユーザー情報がありません！',
                    ], 404, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'エラーが発生しました。もう一度やり直してください！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function getOwner(): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {

                $owner = Owner::where('user_id', $user->id)->first();

                return response()->json([
                    'message' => 'オーナー情報を取得しました!',
                    'owner' => $owner,
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'エラーが発生しました。もう一度やり直してください！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
