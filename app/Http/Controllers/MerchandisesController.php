<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\merchandises;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;

class MerchandisesController extends Controller
{

    public function index($id)
    {
        try {
            if (Gate::allows(Permissions::ALL_PERMISSION)) {

                $merchandises = merchandises::all();

                if ($merchandises->isEmpty()) {
                    return response()->json([
                        "resStatus" => "success",
                        "message" => "初めまして！新規作成ボタンから物販商品を作成しましょう！",
                        'merchandises' => $merchandises
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        "resStatus" => "success",
                        'merchandises' => $merchandises
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
                'message' =>
                '物販商品が見つかりません。'
            ], 500);
        }
    }


    public function store(Request $request, $id)
    {
        try {
            if (Gate::allows(Permissions::MANAGER_PERMISSION)) {
                $validatedData = $request->validate([
                    'merchandise_name' => 'required',
                    'price' => 'required',
                ]);

                $merchandise = merchandises::create([
                    'merchandise_name' => $validatedData['merchandise_name'],
                    'price' => $validatedData['price'],

                ]);

                return response()->json([
                    "resStatus" => "success",
                    "merchandise" => $merchandise

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
                "message" => "物販商品の作成に失敗しました。"
            ], 500);
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $merchandise = merchandises::find($id);

    //         return response()->json([
    //             "resStatus" => "success",
    //             'merchandise' => $merchandise
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             'message' =>
    //             '物販商品が見つかりません。'
    //         ], 500);
    //     }
    // }



    public function update(Request $request, $id)
    {
        try {
            if (Gate::allows(Permissions::MANAGER_PERMISSION)) {

                $validatedData = $request->validate([
                    'merchandise_name' => 'required',
                    'price' => 'required',
                ]);

                $merchandise = merchandises::find($id);


                $merchandise->merchandise_name = $validatedData['merchandise_name'];
                $merchandise->price = $validatedData['price'];

                $merchandise->save();


                return response()->json(
                    [
                        "resStatus" => "success",
                        "merchandise" => $merchandise,
                    ],
                    200
                );
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "物販商品の更新に失敗しました。"
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
                $merchandise = merchandises::find($id);
                if (!$merchandise) {
                    return response()->json([
                        "resStatus" => "error",
                        'message' =>
                        '物販商品が見つかりません。'
                    ], 500);
                }

                $merchandise->delete();
                return response()->json([
                    "resStatus" => "success",
                    "deleteId" => $id
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
                'message' =>
                '物販商品の削除に失敗しました。'
            ], 500);
        }
    }
}
