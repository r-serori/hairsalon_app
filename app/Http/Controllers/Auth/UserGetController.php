<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\User;
use App\Models\staff;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use App\Enums\Roles;
use App\Models\attendance_times;
use Illuminate\Support\Facades\Auth;

class UserGetController extends Controller
{
    public function getUsers($owner_id): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {

                $staffs = staff::where('owner_id', $owner_id)->get();

                if ($staffs->isEmpty()) {
                    return response()->json([
                        'resStatus' => 'success',
                        'message' => 'スタッフ情報がありません!登録してください！',
                        'responseUsers' => []
                    ]);
                } else {

                    $userIds = $staffs->pluck('user_id')->toArray();

                    $users = User::whereIn('id', $userIds)->get();

                    $users->map(function ($user) {
                        return $user->only(['id', 'name', 'phone_number', 'role', 'isAttendance', 'created_at', 'updated_at']);
                    });
                    return response()->json([
                        'resStatus' => 'success',
                        'message' => 'ユーザー情報を取得しました。',
                        'responseUsers' => $users,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'resStatus' => 'error',
                    'message' => '貴方は権限がありません。',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'resStatus' => 'error',
                'message' => 'エラーが発生しました。もう一度やり直してください。',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
    public function getAttendanceUsers($owner_id): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $staffs = staff::where('owner_id', $owner_id)->get();

                if ($staffs->isEmpty()) {
                    return response()->json([
                        'resStatus' => 'success',
                        'message' => 'スタッフ情報がありません!登録してください！',
                        'responseUsers' => []
                    ]);
                } else {

                    $userIds = $staffs->pluck('user_id')->toArray();

                    $users = User::whereIn('id', $userIds)->get();

                    $attendanceTimes = $users->map(function ($userId) {
                        $attendanceTime = attendance_times::where('user_id', $userId->id)->latest()->first();
                        if (empty($attendanceTime)) {
                            return [
                                'user_id' => $userId->id,
                                'start_time' => null,
                                'end_time' => null,
                            ];
                        } else {
                            return $attendanceTime;
                        }
                    });

                    $users->map(function ($user) {
                        return $user->only(['id', 'name', 'isAttendance', 'created_at', 'updated_at']);
                    });
                    return response()->json([
                        'resStatus' => 'success',
                        'message' => 'ユーザー情報を取得しました。',
                        'responseUsers' => $users,
                        'attendanceTimes' => $attendanceTimes,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'resStatus' => 'error',
                    'message' => '貴方は権限がありません。',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'resStatus' => 'error',
                'message' => 'エラーが発生しました。もう一度やり直してください。',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $responseUser = User::find($id);
                if (!empty($responseUser)) {

                    return response()->json([
                        'resStatus' => 'success',
                        'message' => 'ユーザー情報を取得しました。',
                        'responseUser' => $responseUser->only(['id', 'name', 'email', 'phone_number', 'role', 'isAttendance', 'created_at', 'updated_at']),
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'resStatus' => 'error',
                        'message' => 'ユーザー情報がありません。',
                    ], 404, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'resStatus' => 'error',
                    'message' => '貴方は権限がありません。',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'resStatus' => 'error',
                'message' => 'エラーが発生しました。もう一度やり直してください。',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
