<?php

namespace App\Http\Controllers;

use App\Enums\Permissions;
use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;

class OptionsController extends Controller
{

    public function index($id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $user_id = urldecode($id);

                $options = Option::where('owner_id', $user_id)->get();
                if ($options->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！新規作成ボタンからオプションを作成しましょう！",
                        'options' => $options
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'options' => $options
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                'オプションが見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                $validatedData = $request->validate([
                    'option_name' => 'required',
                    'price' => 'required',
                ]);
                $option =
                    Option::create([
                        'option_name' => $validatedData['option_name'],
                        'price' => $validatedData['price'],

                    ]);

                return response()->json([
                    "option" => $option
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "オプションの作成に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $option = Option::find($id);

    //         return response()->json([
    //             'option' => $option
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' =>
    //             'オプションが見つかりません！'
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }

    public function update(Request $request)
    {

        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                $validatedData = $request->validate([
                    'option_name' => 'required',
                    'price' => 'required',
                ]);

                $option = Option::find($request->id);

                $option->option_name = $validatedData['option_name'];
                $option->price = $validatedData['price'];

                $option->save();

                return response()->json(
                    [
                        "option" => $option
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "オプションの更新に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {

        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $option = Option::find($request->id);
                if (!$option) {
                    return response()->json([
                        'message' =>
                        'オプションが見つかりません！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $option->delete();
                return response()->json([
                    'message' => 'オプションを削除しました！
                    もう一度お試しください！',
                    'deleteId' => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                'オプションが見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
