<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\options;

class OptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $options = \App\Models\options::query()
        ->where('option_name', 'like', '%'.$search.'%')
        ->paginate(20)
        ;

        return view('menus.options.index', compact('options'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('menus.options.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'option_name' => 'required',
            'price' => 'required',
        ]);

        options::create([
            'option_name' => $validatedData['option_name'],
            'price' => $validatedData['price'],
        
        ]);
        return redirect()->route('options.index')->with('success', 'オプションの新規作成に成功しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $option = \App\Models\options::find($id);
        return view('menus.options.show', compact('option'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $option = \App\Models\options::find($id);
        return view('menus.options.edit', compact('option'));
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

        $validatedData = $request->validate([
            'option_name' => 'required',
            'price' => 'required',
        ]);

        $option = \App\Models\options::find($id);

        $option->option_name = $validatedData['option_name'];
        $option->price = $validatedData['price'];
        $option->save();

        return redirect()->route('options.index')->with('success', 'オプションの更新に成功しました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Models\options::destroy($id);
        return redirect()->route('options.index')->with('success', 'オプションの削除に成功しました。');
    }
}


