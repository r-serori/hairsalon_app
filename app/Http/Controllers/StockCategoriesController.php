<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stock_categories;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Roles;


class StockCategoriesController extends Controller
{
    public function index($id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $user_id = urldecode($id);
                // カテゴリ一覧を取得
                $stock_categories = stock_categories::where('owner_id', $user_id)->get();
                if ($stock_categories->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！新規作成ボタンから在庫カテゴリを作成しましょう！",
                        'stockCategories' => $stock_categories
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'stockCategories' => $stock_categories
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => '在庫カテゴリが見つかりません！
                もう一度お試しください！'
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
                    'category' => 'required|string',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);


                // 在庫モデルを作成して保存する
                $stock_category = stock_categories::create([
                    'category' => $validatedData['category'],
                    'owner_id' => $validatedData['owner_id'],
                ]);


                // 成功したらリダイレクト
                return response()->json([
                    "stockCategory" => $stock_category
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "在庫カテゴリの作成に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    // public function show($id)
    // {
    //     try {
    //         if (Gate::allows(Permissions::MANAGER_PERMISSION)) {
    //             // 指定されたIDの在庫カテゴリを取得
    //             $stock_category = stock_categories::find($id);

    //             // 在庫カテゴリを表示
    //             return response()->json([
    //                 'stockCategory' => $stock_category
    //             ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         } else {
    //             return response()->json([
    //                 "message" => "あなたには権限がありません！""
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => '在庫カテゴリが見つかりません！'
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
                    'category' => 'required|string',
                ]);

                // 在庫を取得する
                $stock_category = stock_categories::find($request->id);

                // 在庫の属性を更新する
                $stock_category->category = $validatedData['category'];


                // 在庫を保存する
                $stock_category->save();

                // 成功したらリダイレクト
                return response()->json([
                    "stockCategory" => $stock_category
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "在庫カテゴリの更新に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                // 指定されたIDの在庫カテゴリを取得
                $stock_category = stock_categories::find($request->id);

                // 在庫カテゴリが見つからない場合は404エラーを返す
                if (!$stock_category) {
                    return response()->json([
                        'message' =>
                        '在庫カテゴリが見つかりません！
                        もう一度お試しください！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                // 在庫カテゴリを削除する
                $stock_category->delete();
                return response()->json([
                    "deleteId" => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            // エラーが発生した場合は500エラーを返す
            return response()->json([
                'message' =>
                '在庫カテゴリの削除に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
