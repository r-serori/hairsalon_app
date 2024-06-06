<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stock_categories;


class StockCategoriesController extends Controller
{
    public function index()
    {
        try {
            // カテゴリー一覧を取得
            $stock_categories = stock_categories::all();
            if ($stock_categories->isEmpty()) {
                return response()->json([
                    "resStatus" => "success",
                    "message" => "初めまして！新規作成ボタンから在庫カテゴリーを作成しましょう！",
                    'stockCategories' => $stock_categories
                ], 200);
            } else {
                return response()->json([
                    "resStatus" => "success",
                    'stockCategories' => $stock_categories
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => 'ストックカテゴリーが見つかりません。'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // バリデーションルールを定義する
            $validatedData = $request->validate([
                'category' => 'required|string',
            ]);


            // 在庫モデルを作成して保存する
            $stock_category = stock_categories::create([
                'category' => $validatedData['category'],
            ]);


            // 成功したらリダイレクト
            return response()->json([
                "resStatus" => "success",
                "stockCategory" => $stock_category
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "在庫カテゴリーの作成に失敗しました。"
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            // 指定されたIDの在庫カテゴリーを取得
            $stock_category = stock_categories::find($id);

            // 在庫カテゴリーを表示
            return response()->json([
                "resStatus" => "success",
                'stockCategory' => $stock_category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => 'ストックカテゴリーが見つかりません。'
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        try {
            // バリデーションルールを定義する
            $validatedData = $request->validate([
                'category' => 'required|string',
            ]);

            // 在庫を取得する
            $stock_category = stock_categories::find($id);

            // 在庫の属性を更新する
            $stock_category->category = $validatedData['category'];


            // 在庫を保存する
            $stock_category->save();

            // 成功したらリダイレクト
            return response()->json([
                "resStatus" => "success",
                "stockCategory" => $stock_category
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "在庫カテゴリーの更新に失敗しました。"
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // 指定されたIDの在庫カテゴリーを取得
            $stock_category = stock_categories::find($id);

            // 在庫カテゴリーが見つからない場合は404エラーを返す
            if (!$stock_category) {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    'ストックカテゴリーが見つかりません。'
                ], 500);
            }

            // 在庫カテゴリーを削除する
            $stock_category->delete();
            return response()->json([
                "resStatus" => "success",
                "deleteId" => $id
            ], 200);
        } catch (\Exception $e) {
            // エラーが発生した場合は500エラーを返す
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'ストックカテゴリーの削除に失敗しました。'
            ], 500);
        }
    }
}
