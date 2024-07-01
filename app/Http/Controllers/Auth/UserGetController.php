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
    public function getUsers($user_id): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $decodedUserId = urldecode($user_id);

                $owner = Owner::find($decodedUserId);

                $staffs = Staff::where('owner_id', $owner->id)->get();

                if ($staffs->isEmpty()) {
                    return response()->json([
                        'message' => 'スタッフ情報がありません!登録してください！',
                        'responseUsers' => []
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {

                    $userIds = $staffs->pluck('user_id')->toArray();

                    $users = User::whereIn('id', $userIds)->get();

                    $users->map(function ($user) {
                        return $user->only(['id', 'name', 'phone_number',  'isAttendance']);
                    });

                    $userCount = count($users);

                    return response()->json([
                        'message' => 'ユーザー情報を取得しました!',
                        'responseUsers' => $users,
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
    public function getAttendanceUsers($user_id): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                $decodedUserId = urldecode($user_id);

                $owner = Owner::find($decodedUserId);

                $staffs = Staff::where('owner_id', $owner->id)->get();

                if ($staffs->isEmpty()) {
                    return response()->json([
                        'message' => 'スタッフ情報がありません!登録してください！',
                        'responseUsers' => []
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {

                    $userIds = $staffs->pluck('user_id')->toArray();

                    $users = User::whereIn('id', $userIds)->get();



                    $attendanceTimes = $users->map(function ($userId) {
                        $attendanceTime = AttendanceTime::where('user_id', $userId->id)->latest()->first();
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
                        return $user->only(['id', 'name', 'isAttendance']);
                    });

                    $userCount = count($users);

                    return response()->json([
                        'message' => 'ユーザー情報を取得しました！',
                        'responseUsers' => $users,
                        'attendanceTimes' => $attendanceTimes,
                        'userCount' => $userCount,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return response()->json([
                'message' => 'エラーが発生しました。もう一度やり直してください！',
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function show($user_id): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                $decodedUserId = urldecode($user_id);

                $responseUser = User::find($decodedUserId);
                if (!empty($responseUser)) {

                    $responseUser->only(['id', 'name', 'email', 'phone_number', 'isAttendance']);

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
}
