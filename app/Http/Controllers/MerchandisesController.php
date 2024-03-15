<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\merchandises;

class MerchandisesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $merchandises = \App\Models\merchandises::query()
        ->where('merchandise_name', 'like', '%'.$search.'%')
        ->paginate(20);

        return view('menus.merchandises.index', compact('merchandises'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('menus.merchandises.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \App\Models\merchandises::create($request->all());
        return redirect()->route('merchandises.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $merchandise = \App\Models\merchandises::find($id);
        return view('menus.merchandises.show', compact('merchandise'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $merchandise = \App\Models\merchandises::find($id);
        return view('menus.merchandises.edit', compact('merchandise'));
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
        \App\Models\merchandises::find($id)->update($request->all());
        return redirect()->route('merchandises.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Models\merchandises::destroy($id);
        return redirect()->route('merchandises.index');
    }
}


