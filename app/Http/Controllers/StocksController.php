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
        try {
            $stocks = stocks::all();
            if ($stocks->isEmpty()) {
                return response()->json([
                    "resStatus" => "success",
                    "message" => "初めまして！新規作成ボタンから店の在庫を作成しましょう！",
                    'stocks' => $stocks
                ], 200);
            } else {
                return response()->json([
                    "resStatus" => "success",
                    'stocks' => $stocks
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '在庫が見つかりませんでした。'
            ], 500);
        }
    }



    public function store(Request $request)
    {
        try {
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
            $stocks =  stocks::create([
                'product_name' => $validatedData['product_name'],
                'quantity' => $validatedData['quantity'],
                'product_price' => $validatedData['product_price'],
                'supplier' => $validatedData['supplier'],
                'remarks' => $validatedData['remarks'],
                'stock_category_id' => $validatedData['stock_category_id'],
            ]);


            // 成功したらリダイレクト
            return response()->json([
                "resStatus" => "success",
                "stock" => $stocks
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '在庫の登録に失敗しました。'
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            // 指定されたIDの在庫を取得
            $stock = stocks::find($id);

            // 在庫を表示
            return response()->json([
                "error" => "success",
                'stock' => $stock
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '在庫が見つかりませんでした。'
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        try {
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
            $stock = stocks::find($id);

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
            return response()->json([
                "resStatus" => "success",
                "stock" => $stock
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '在庫の更新に失敗しました。'
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $stock = stocks::find($id);
            if (!$stock) {
                return response()->json([
                    "resStatus" => "error",
                    'message' => '在庫が見つかりませんでした。'
                ], 500);
            }

            $stock->delete();
            return response()->json([
                "resStatus" => "success",
                "deleteId"  => $id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '在庫の削除に失敗しました。'
            ], 500);
        }
    }
}
