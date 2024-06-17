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
                })
            ]);
        }
    }
}
