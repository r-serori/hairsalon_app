<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Hairstyle;
use App\Models\Course;
use App\Models\Option;
use App\Models\Merchandise;




class CustomersController extends Controller{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hairstyles = Hairstyle::all();
        $courses = Course::all();
        $options = Option::all();
        $merchandises = Merchandise::all();
        
        // 顧客データを取得し、各外部キーに対応するモデルのインスタンスをロードする
        $customers = Customers::with('hairstyle', 'course', 'option', 'merchandise')->get();
    
        return view('jobs.customers.index', compact('customers', 'hairstyles', 'courses', 'options', 'merchandises'));
    }
    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hairstyles = Hairstyle::all();
        $courses = Course::all();
        $options = Option::all();
        $merchandises = Merchandise::all();

        return view('jobs.customers.create', compact('hairstyles', 'courses', 'options', 'merchandises'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Customers::create($request->all());
        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Customers::find($id);
        return view('jobs.customers.show', compact('customers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        Customers::find($id);
        return view('jobs.customers.edit', compact('customers'));
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
        Customers::find($id)->update($request->all());
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Customers::find($id)->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
