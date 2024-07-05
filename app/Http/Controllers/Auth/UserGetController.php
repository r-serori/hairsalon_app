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
            if ($user && $user->hasRole(Roles::$OWNER)) {

                $owner = Owner::where('user_id', $user->id)->first();

                $staffs = Staff::where('owner_id', $owner->id)->get();


                if ($staffs->isEmpty()) {
                    return response()->json([
                        'message' => 'スタッフ情報がありません!登録してください！',
                        'responseUsers' => [],
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {

                    $userIds = $staffs->pluck('user_id');

                    $userIds->push($user->id);

                    $users = User::whereIn('id', $userIds)->get();

                    $responseUsers = $users->map(function ($usera) {
                        return [
                            'id' => $usera->id,
                            'name' => $usera->name,
                            'email' => $usera->email,
                            'phone_number' => $usera->phone_number,
                            'role' => $usera->role === Roles::$OWNER ? 'オーナー' : ($usera->role === Roles::$MANAGER ? 'マネージャー' : 'スタッフ'),
                            'isAttendance' => $usera->isAttendance,
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
    public function getAttendanceUsers(): JsonResponse
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

                $owner = Owner::find($ownerId);

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
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone_number' => $user->phone_number,
                            'role' => $user->role === Roles::$MANAGER ? 'マネージャー' : 'スタッフ',
                            'isAttendance' => $user->isAttendance,
                        ];
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
}
