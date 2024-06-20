<?php

namespace App\Http\Controllers;

use App\Enums\Permissions;
use Illuminate\Http\Request;
use App\Models\options;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;

class OptionsController extends Controller
{

    public function index($id)
    {
        try {
            if (Gate::allows(Permissions::ALL_PERMISSION)) {
                $options = options::where('owner_id', $id)->get();
                if ($options->isEmpty()) {
                    return response()->json([
                        "resStatus" => "success",
                        "message" => "初めまして！新規作成ボタンからオプションを作成しましょう！",
                        'options' => $options
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        "resStatus" => "success",
                        'options' => $options
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'オプションが見つかりません。       '
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function store(Request $request)
    {
        try {
            if (Gate::allows(Permissions::MANAGER_PERMISSION)) {
                $validatedData = $request->validate([
                    'option_name' => 'required',
                    'price' => 'required',
                ]);
                $option =
                    options::create([
                        'option_name' => $validatedData['option_name'],
                        'price' => $validatedData['price'],

                    ]);

                return response()->json([
                    "resStatus" => "success",
                    "option" => $option
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "オプションの作成に失敗しました。"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $option = options::find($id);

    //         return response()->json([
    //             "resStatus" => "success",
    //             'option' => $option
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             'message' =>
    //             'オプションが見つかりません。'
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }

    public function update(Request $request)
    {

        try {
            if (Gate::allows(Permissions::MANAGER_PERMISSION)) {
                $validatedData = $request->validate([
                    'option_name' => 'required',
                    'price' => 'required',
                ]);

                $option = options::find($request->id);

                $option->option_name = $validatedData['option_name'];
                $option->price = $validatedData['price'];

                $option->save();

                return response()->json(
                    [
                        "resStatus" => "success",
                        "option" => $option
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "オプションの更新に失敗しました。"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {

        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
                $option = options::find($request->id);
                if (!$option) {
                    return response()->json([
                        "resStatus" => "error",
                        'message' =>
                        'オプションが見つかりません。'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $option->delete();
                return response()->json([
                    "resStatus" => "success",
                    'message' => 'オプションを削除しました。',
                    'deleteId' => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'オプションが見つかりません。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
