<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\hairstyles;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Http\JsonResponse;

class HairstylesController extends Controller
{

    public function index($id): JsonResponse
    {
        try {
            if (Gate::allows(Permissions::ALL_PERMISSION)) {
                $hairstyles = hairstyles::where('owner_id', $id)->get();

                if ($hairstyles->isEmpty()) {
                    return response()->json([
                        "resStatus" => "success",
                        "message" => "初めまして！新規作成ボタンから使用するヘアスタイルを作成しましょう！",
                        'hairstyles' => $hairstyles
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        "resStatus" => "success",
                        'hairstyles' => $hairstyles
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => "ヘアスタイルが見つかりませんでした。"
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            if (Gate::allows(Permissions::MANAGER_PERMISSION)) {
                $validatedData = $request->validate([
                    'hairstyle_name' => 'required',
                ]);

                $hairstyle = hairstyles::create([
                    'hairstyle_name' => $validatedData['hairstyle_name'],
                ]);

                return response()->json([
                    "resStatus" => "success",
                    "hairstyle" => $hairstyle
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "ヘアスタイルの作成に失敗しました。"
            ], 500);
        }
    }

    // public function show($id)
    // {
    //     try {

    //         $hairstyle = hairstyles::find($id);
    //         if (!$hairstyle) {
    //             return response()->json([
    //                 'resStatus' => 'error',
    //                 'message' => 'ヘアスタイルが見つかりません。'
    //             ], 404);
    //         }

    //         return response()->json([
    //             "resStatus" => "success",
    //             'hairstyle' => $hairstyle
    //         ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             'message' => 'ヘアスタイルが見つかりません。'
    //         ], 500);
    //     }
    // }



    public function update(Request $request)
    {
        try {
            if (Gate::allows(Permissions::MANAGER_PERMISSION)) {
                $validatedData = $request->validate([
                    'hairstyle_name' => 'required',
                ]);

                $hairstyle = hairstyles::find($request->id);
                $hairstyle->hairstyle_name = $validatedData['hairstyle_name'];

                $hairstyle->save();

                return response()->json(
                    [
                        "resStatus" => "success",
                        "hairstyle" => $hairstyle
                    ],
                    200
                );
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません。"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "ヘアスタイルの更新に失敗しました。"
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
                $hairstyle = hairstyles::find($request->id);
                if (!$hairstyle) {
                    return response()->json([
                        "resStatus" => "error",
                        'message' => 'ヘアスタイルが見つかりません。'
                    ], 500);
                }
                $hairstyle->delete();
                return response()->json([
                    "resStatus" => "success",
                    "deleteId" => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません。"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => 'ヘアスタイルの削除に失敗しました。'
            ], 500);
        }
    }
}
