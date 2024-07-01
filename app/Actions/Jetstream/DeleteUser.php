<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles;

class DeleteUserMain
{
    public function deleteUser(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {

                $deleteUser = User::find($request->id); // 例: リクエストからユーザーIDを取得

                if (!$deleteUser) {
                    return response()->json([
                        'resStatus' => 'error',
                        'message' => 'ユーザーが見つかりませんでした。',
                    ], 404, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $deleteUser->delete();

                return response()->json([
                    'resStatus' => 'success',
                    'message' => 'ユーザーの削除に成功しました。',
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    'resStatus' => 'error',
                    'message' => 'あなたは権限がありません。',
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
