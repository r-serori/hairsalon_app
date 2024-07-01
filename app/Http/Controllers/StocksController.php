<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                $user_id = urldecode($id);
                $stocksCacheKey = 'owner_' . $user_id . 'stocks';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $stocks = Cache::remember($stocksCacheKey, $expirationInSeconds, function () use ($user_id) {
                    return Stock::where('owner_id', $user_id)->get();
                });
                if ($stocks->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！新規作成ボタンから店の在庫を作成しましょう！",
                        'stocks' => $stocks
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'stocks' => $stocks
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたに権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => '在庫が見つかりませんでした！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }



    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
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
                $stocks =  Stock::create([
                    'product_name' => $validatedData['product_name'],
                    'quantity' => $validatedData['quantity'],
                    'product_price' => $validatedData['product_price'],
                    'supplier' => $validatedData['supplier'],
                    'remarks' => $validatedData['remarks'],
                    'notice' => $validatedData['notice'],
                    'stock_category_id' => $validatedData['stock_category_id'],
                    'owner_id' => $validatedData['owner_id'],
                ]);

                $stocksCacheKey = 'owner_' . $request->owner_id . 'stocks';

                Cache::forget($stocksCacheKey);
                // 成功したらリダイレクト
                return response()->json([
                    "stock" => $stocks
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたに権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => '在庫の登録に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    // public function show($id)
    // {
    //     try {
    //         if (Gate::allows(Permissions::$MANAGER_PERMISSION)) {
    //             // 指定されたIDの在庫を取得
    //             $stock = Stock::find($id);

    //             // 在庫を表示
    //             return response()->json([
    //                 "error" => "success",
    //                 'stock' => $stock
    //             ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         } else {
    //             return response()->json([
    //                 "message" => "あなたに権限がありません！"
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => '在庫が見つかりませんでした！'
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }



    public function update(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
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
                $stock = Stock::find($request->id);

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
                $stocksCacheKey = 'owner_' . $request->owner_id . 'stocks';

                Cache::forget($stocksCacheKey);
                // 成功したらリダイレクト
                return response()->json([
                    "stock" => $stock
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたに権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => '在庫の更新に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $stock = Stock::find($request->id);
                if (!$stock) {
                    return response()->json([
                        'message' => '在庫が見つかりませんでした！
                        もう一度お試しください！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $stock->delete();
                $stocksCacheKey = 'owner_' . $request->owner_id . 'stocks';

                Cache::forget($stocksCacheKey);
                return response()->json([
                    "deleteId"  => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたに権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => '在庫の削除に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
