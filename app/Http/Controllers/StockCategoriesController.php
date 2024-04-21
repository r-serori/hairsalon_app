<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stock_categories;


class StockCategoriesController extends Controller
{
    public function index()
    {

        // カテゴリー一覧を取得
        $stock_categories = stock_categories::all();

        // 在庫一覧ページにデータを渡して表示
        return response()->json(['stock_categories' => $stock_categories]);
    }

    public function store(Request $request)
    {
        // バリデーションルールを定義する
        $validatedData = $request->validate([
            'category' => 'required|string',
        ]);


        // 在庫モデルを作成して保存する
        stock_categories::create([
            'category' => $validatedData['category'],
        ]);


        // 成功したらリダイレクト
        return response()->json([], 204);
    }


    public function show($id)
    {
        // 指定されたIDの在庫カテゴリーを取得
        $stock_category = stock_categories::find($id);

        // 在庫カテゴリーを表示
        return response()->json(['stock_category' => $stock_category]);
    }



    public function update(Request $request, $id)
    {
        // バリデーションルールを定義する
        $validatedData = $request->validate([
            'category' => 'required|string',
        ]);


        // 在庫を取得する
        $stock_category = stock_categories::findOrFail($id);

        // 在庫の属性を更新する
        $stock_category->category = $validatedData['category'];


        // 在庫を保存する
        $stock_category->save();

        // 成功したらリダイレクト
        return response()->json([], 204);
    }

    public function destroy($id)
    {
        // 指定されたIDの在庫カテゴリーを取得
        $stock_category = stock_categories::find($id);

        // 在庫カテゴリーが見つからない場合は404エラーを返す
        if (!$stock_category) {
            return response()->json(['message' =>
            'stock_category not found'], 404);
        }

        try {
            // 在庫カテゴリーを削除する
            $stock_category->delete();
            return response()->json([], 204);
        } catch (\Exception $e) {
            // エラーが発生した場合は500エラーを返す
            return response()->json(['message' =>
            'failed to delete the stock_category'], 500);
        }
    }
}
