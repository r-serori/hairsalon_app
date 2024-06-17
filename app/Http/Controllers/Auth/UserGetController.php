<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\User;
use App\Models\staff;

class UserGetController extends Controller
{
    public function getUsers($owner_id, $user_id): JsonResponse
    {

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
    }

    public function show($user_id): JsonResponse
    {
        $user = User::where('id', $user_id)->first();

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
    }
}
