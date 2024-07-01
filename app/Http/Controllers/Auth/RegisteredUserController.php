<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;


use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    // public function store(Request $request): JsonResponse
    // {
    //     try {
    //         Log::info('ユーザー登録処理を開始します。', $request->toArray());

    //         $input = $request->all();

    //         // バリデーション
    //         $validator = Validator::make($input, [
    //             'name' => 'required|string|max:255',
    //             'email' => 'required|string|email|max:255|unique:users',
    //             'phone_number' => 'required|string|max:20',
    //             'password' => 'required|string|min:8|confirmed',
    //             'role' => 'required|string',
    //             'isAttendance' => 'required|boolean',
    //         ]);

    //         if ($validator->fails()) {
    //             throw new ValidationException($validator);
    //         }

    //         // ユーザーの作成
    //         $user = User::create([
    //             'name' => $request->name,
    //             'email' => $request->email,
    //             'phone_number' => $request->phone_number,
    //             'password' => Hash::make($request->password),
    //             'role' => $request->role,
    //             'isAttendance' => $request->isAttendance,
    //         ]);

    //         // ユーザーが作成された場合のレスポンス
    //         if (!$user) {
    //             return response()->json([
    //                 "resStatus" => 'error',
    //                 'message' => 'ユーザー登録に失敗しました。初めからやり直してください。',
    //             ], 400);
    //         }

    //         // ログイン、イベントの発行
    //         Auth::login($user);

    //         event(new Registered($user));

    //         $user->session()->regenerate();

    //         return response()->json(
    //             [
    //                 'resStatus' => "success",
    //                 'message' => 'ユーザー登録に成功しました!',
    //                 'responseUser' => $user->only(['id', 'name', 'email', 'phone_number', 'isAttendance', 'created_at', 'updated_at'])
    //             ],
    //             200
    //         );
    //     } catch (ValidationException $e) {
    //         Log::error('バリデーションエラー: ' . implode(', ', $e->errors()));
    //         return response()->json([
    //             "resStatus" => 'error',
    //             'message' => 'バリデーションエラー: ' . implode(', ', $e->errors()),
    //         ], 422);
    //     } catch (\Exception $e) {
    //         Log::error('ユーザー登録処理中にエラーが発生しました。');
    //         Log::error('エラー内容: ' . $e);
    //         return response()->json([
    //             "resStatus" => 'error',
    //             'message' => 'エラーが発生しました。 エラー内容：' . $e->getMessage(),
    //         ], 400);
    //     }
    // }
}
