<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stocks;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;

class StocksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return  JsonResponse
     */

    public function index($id): JsonResponse
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $stocks = stocks::where('owner_id', $id)->get();
                if ($stocks->isEmpty()) {
                    return response()->json([
                        "resStatus" => "success",
                        "message" => "初めまして！新規作成ボタンから店の在庫を作成しましょう！",
                        'stocks' => $stocks
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        "resStatus" => "success",
                        'stocks' => $stocks
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
                'message' => '在庫が見つかりませんでした。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }



    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                // バリデーションルールを定義する
                $validatedData = $request->validate([
                    'product_name' => 'required',
                    'quantity' => 'required|integer',
                    'product_price' => 'required|integer',
                    'supplier' => 'nullable|string',
                    'remarks' => 'nullable|string',
                    "notice" => "required|integer",
                    'stock_category_id' => 'required|exists:stock_categories,id',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                // 在庫モデルを作成して保存する
                $stocks =  stocks::create([
                    'product_name' => $validatedData['product_name'],
                    'quantity' => $validatedData['quantity'],
                    'product_price' => $validatedData['product_price'],
                    'supplier' => $validatedData['supplier'],
                    'remarks' => $validatedData['remarks'],
                    'notice' => $validatedData['notice'],
                    'stock_category_id' => $validatedData['stock_category_id'],
                    'owner_id' => $validatedData['owner_id'],
                ]);


                // 成功したらリダイレクト
                return response()->json([
                    "resStatus" => "success",
                    "stock" => $stocks
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
                'message' => '在庫の登録に失敗しました。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    // public function show($id)
    // {
    //     try {
    //         if (Gate::allows(Permissions::MANAGER_PERMISSION)) {
    //             // 指定されたIDの在庫を取得
    //             $stock = stocks::find($id);

    //             // 在庫を表示
    //             return response()->json([
    //                 "error" => "success",
    //                 'stock' => $stock
    //             ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         } else {
    //             return response()->json([
    //                 "resStatus" => "error",
    //                 "message" => "権限がありません"
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             'message' => '在庫が見つかりませんでした。'
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }



    public function update(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                // バリデーションルールを定義する
                $validatedData = $request->validate([
                    'product_name' => 'required',
                    'quantity' => 'required|integer',
                    'product_price' => 'required|integer',
                    'supplier' => 'nullable|string',
                    'remarks' => 'nullable|string',
                    "notice" => "required|integer",
                    'stock_category_id' => 'required|exists:stock_categories,id',
                ]);

                // 在庫を取得する
                $stock = stocks::find($request->id);

                // 在庫の属性を更新する
                $stock->product_name = $validatedData['product_name'];
                $stock->quantity = $validatedData['quantity'];
                $stock->product_price = $validatedData['product_price'];
                $stock->supplier = $validatedData['supplier'];
                $stock->remarks = $validatedData['remarks'];
                $stock->notice = $validatedData['notice'];
                $stock->stock_category_id = $validatedData['stock_category_id'];

                // 在庫を保存する
                $stock->save();

                // 成功したらリダイレクト
                return response()->json([
                    "resStatus" => "success",
                    "stock" => $stock
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
                'message' => '在庫の更新に失敗しました。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $stock = stocks::find($request->id);
                if (!$stock) {
                    return response()->json([
                        "resStatus" => "error",
                        'message' => '在庫が見つかりませんでした。'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $stock->delete();
                return response()->json([
                    "resStatus" => "success",
                    "deleteId"  => $request->id
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
                'message' => '在庫の削除に失敗しました。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
