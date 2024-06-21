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

                    return response()->json([
                        'resStatus' => 'success',
                        'message' => 'ユーザー情報を取得しました。',
                        'responseUsers' => $users->map(function ($user) {
                            return $user->only(['id', 'name', 'phone_number', 'role', 'isAttendance', 'created_at', 'updated_at']);
                        }),
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

    public function show(): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                if (!empty($user)) {
                    return response()->json([
                        'resStatus' => 'success',
                        'message' => 'ユーザー情報を取得しました。',
                        'responseUser' => $user->only('id', 'name', 'email', 'phone_number', 'role', 'isAttendance', 'created_at', 'updated_at'),
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
