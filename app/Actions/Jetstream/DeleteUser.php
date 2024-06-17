<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Illuminate\Http\Request;

class DeleteUserMain
{
    public function deleteUser(Request $request)
    {
        $user = User::find($request->id); // 例: リクエストからユーザーIDを取得

        if (!$user) {
            return response()->json([
                'resStatus' => 'error',
                'message' => 'ユーザーが見つかりませんでした。',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'resStatus' => 'success',
            'message' => 'ユーザーの削除に成功しました。',
        ], 200);
    }
}
