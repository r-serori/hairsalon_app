<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\hairstyles;

class HairstylesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hairstyles = \App\Models\hairstyles::all();
        return view('menus.hairstyles.index', compact('hairstyles'));

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('menus.hairstyles.create');
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\Models\hairstyles::create($request->all());
        return redirect()->route('hairstyles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $hairstyle = \App\Models\hairstyles::find($id);
        return view('menus.hairstyles.show', compact('hairstyle'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $hairstyle = \App\Models\hairstyles::find($id);
        return view('menus.hairstyles.edit', compact('hairstyle'));
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
        \App\Models\hairstyles::find($id)->update($request->all());
        return redirect()->route('hairstyles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    
        \App\Models\hairstyles::destroy($id);
        return redirect()->route('hairstyles.index');
    }
}

