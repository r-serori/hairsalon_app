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
        return view('stores.stock_categories.index', compact('stock_categories'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('stores.stock_categories.create');
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
            'category' => 'required|string',
        ]);


        // 在庫モデルを作成して保存する
        stock_categories::create([
            'category' => $validatedData['category'],
        ]);


        // 成功したらリダイレクト
        return redirect()->route('stock_categories.index')->with('success', '在庫カテゴリーの新規作成に成功しました。');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stock_category = \App\Models\stock_categories::find($id);
        
        return view('stores.stock_categories.edit', compact('stock_category'));
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
                'category' => 'required|string',
            ]);
        

        // 在庫を取得する
        $stock_category = stock_categories::findOrFail($id);

        // 在庫の属性を更新する
        $stock_category->category= $validatedData['category'];


        // 在庫を保存する
        $stock_category->save();

        // 成功したらリダイレクト
        return redirect()->route('stock_categories.index')->with('success', '在庫カテゴリーが更新されました');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Models\stock_categories::destroy($id);
        return redirect()->route('stock_categories.index')->with('success', '在庫カテゴリーを削除しました。');
    }
}


