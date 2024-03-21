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

    public function index(Request $request)
    {

        // カテゴリー一覧を取得
        $stock_categories = stock_categories::all();

        // カテゴリーIDと商品名を取得
        $categoryId = $request->input('category');
        $productName = $request->input('search');

        // 在庫データを取得するクエリを作成
        $query = stocks::query()->with('stock_category');

    

        // カテゴリーでフィルタリング
        if ($categoryId) {
            $query->where('stock_category_id', $categoryId);
        }

        // 商品名で部分一致検索
        if ($productName) {
            $query->where('product_name', 'like', '%' . $productName . '%');
        }

        // 在庫データを取得
        $stocks = $query->get();



        // 在庫一覧ページにデータを渡して表示
        return view('stores.stocks.index', compact('stocks', 'stock_categories'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stock_categories = \App\Models\stock_categories::all();

        return view('stores.stocks.create', compact('stock_categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
        return redirect()->route('stocks.index')->with('success', '在庫の新規作成に成功しました');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $stock = \App\Models\stocks::find($id);
        return view('stores.stocks.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stock = \App\Models\stocks::find($id);
        $stock_categories = stock_categories::all();
        return view('stores.stocks.edit', compact('stock', 'stock_categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        return redirect()->route('stocks.index')->with('success', '在庫の更新に成功しました');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Models\stocks::destroy($id);
        return redirect()->route('stocks.index')->with('success', '在庫を削除しました。');
    }
}
