<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stocks;
use App\Models\stock_categories;

class StocksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        $stocks = stocks::all();
        return response()->json(['stocks' => $stocks]);
    }


    public function store(Request $request)
    {
        // バリデーションルールを定義する
        $validatedData = $request->validate([
            'product_name' => 'required',
            'quantity' => 'required|integer',
            'product_price' => 'required|integer',
            'supplier' => 'nullable|string',
            'remarks' => 'nullable|string',
            'stock_category_id' => 'required|exists:stock_categories,id',
        ]);

        // 在庫モデルを作成して保存する
        stocks::create([
            'product_name' => $validatedData['product_name'],
            'quantity' => $validatedData['quantity'],
            'product_price' => $validatedData['product_price'],
            'supplier' => $validatedData['supplier'],
            'remarks' => $validatedData['remarks'],
            'stock_category_id' => $validatedData['stock_category_id'],
        ]);


        // 成功したらリダイレクト
        return response()->json([], 204);
    }


    public function show($id)
    {
        // 指定されたIDの在庫を取得
        $stock = stocks::findOrFail($id);

        // 在庫を表示
        return response()->json(['stock' => $stock]);
    }

    public function edit($id)
    {
        // 指定されたIDの在庫を取得
        $stock = stocks::findOrFail($id);

        // 在庫を表示
        return response()->json(['stock' => $stock]);
    }


    public function update(Request $request, $id)
    {
        // バリデーションルールを定義する
        $validatedData = $request->validate([
            'product_name' => 'required',
            'quantity' => 'required|integer',
            'product_price' => 'required|integer',
            'supplier' => 'nullable|string',
            'remarks' => 'nullable|string',
            'stock_category_id' => 'required|exists:stock_categories,id',
        ]);

        // 在庫を取得する
        $stock = stocks::findOrFail($id);

        // 在庫の属性を更新する
        $stock->product_name = $validatedData['product_name'];
        $stock->quantity = $validatedData['quantity'];
        $stock->product_price = $validatedData['product_price'];
        $stock->supplier = $validatedData['supplier'];
        $stock->remarks = $validatedData['remarks'];
        $stock->stock_category_id = $validatedData['stock_category_id'];

        // 在庫を保存する
        $stock->save();

        // 成功したらリダイレクト
        return response()->json([], 204);
    }


    public function destroy($id)
    {
        $stock = stocks::find($id);
        if (!$stock) {
            return response()->json(['message' => '在庫が見つかりませんでした。'], 404);
        }

        try {
            $stock->delete();
            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json(['message' => '在庫の削除に失敗しました。'], 500);
        }
    }
}
