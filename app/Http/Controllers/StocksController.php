<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\stocks;

class StocksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        $stocks = stocks::query()
            ->where('product_name', 'like', '%'.$search.'%')
            ->orWhere('category', 'like', '%'.$search.'%')
            ->paginate(20);
    
        return view('stores.stocks.index', compact('stocks'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('stores.stocks.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\Models\stocks::create($request->all());
        return redirect()->route('stocks.index')->with('success', '在庫を登録しました。');
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
        return view('stores.stocks.edit', compact('stock'));
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
        \App\Models\stocks::find($id)->update($request->all());
        return redirect()->route('stocks.index')->with('success', '在庫を更新しました。');
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


