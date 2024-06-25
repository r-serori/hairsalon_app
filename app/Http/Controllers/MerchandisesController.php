<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\merchandises;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Roles;


class MerchandisesController extends Controller
{

    public function index($id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $merchandises = merchandises::where('owner_id', $id)->get();

                if ($merchandises->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！新規作成ボタンから物販商品を作成しましょう！",
                        'merchandises' => $merchandises
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'merchandises' => $merchandises
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                '物販商品が見つかりません！
                もう一度お試しください！'
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $validatedData = $request->validate([
                    'merchandise_name' => 'required',
                    'price' => 'required',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                $merchandise = merchandises::create([
                    'merchandise_name' => $validatedData['merchandise_name'],
                    'price' => $validatedData['price'],
                    'owner_id' => $validatedData['owner_id'],

                ]);

                return response()->json([
                    "merchandise" => $merchandise

                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "物販商品の作成に失敗しました！
                もう一度お試しください！"
            ], 500);
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $merchandise = merchandises::find($id);

    //         return response()->json([
    //             'merchandise' => $merchandise
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' =>
    //             '物販商品が見つかりません！'
    //         ], 500);
    //     }
    // }



    public function update(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {

                $validatedData = $request->validate([
                    'merchandise_name' => 'required',
                    'price' => 'required',
                ]);

                $merchandise = merchandises::find($request->id);


                $merchandise->merchandise_name = $validatedData['merchandise_name'];
                $merchandise->price = $validatedData['price'];

                $merchandise->save();


                return response()->json(
                    [
                        "merchandise" => $merchandise,
                    ],
                    200
                );
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "物販商品の更新に失敗しました！
                もう一度お試しください！"
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $merchandise = merchandises::find($request->id);
                if (!$merchandise) {
                    return response()->json([
                        'message' =>
                        '物販商品が見つかりません！
                        もう一度お試しください！'
                    ], 500);
                }

                $merchandise->delete();
                return response()->json([
                    "deleteId" => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                '物販商品の削除に失敗しました！
                もう一度お試しください！'
            ], 500);
        }
    }
}
