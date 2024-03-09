<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Hairstyle;
use App\Models\Course;
use App\Models\Option;
use App\Models\Merchandise;
use App\Models\User;




class CustomersController extends Controller{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // 顧客データを取得し、各外部キーに対応するモデルのインスタンスをロードする
        $customers = Customers::with('user','hairstyle', 'course', 'option', 'merchandise')->get();
    
        return view('jobs.customers.index', compact('customers'));
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
        $users = User::all();

        return view('jobs.customers.create', compact('users','hairstyles', 'courses', 'options', 'merchandises'));
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
             // 指定されたIDの顧客データを取得
             $customer = Customers::findOrFail($id);
        
             // showビューにデータを渡して表示
             return view('jobs.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customers::findOrFail($id);

        // すべての髪型、コース、オプション、物販データを取得
        $hairstyles = Hairstyle::all();
        $courses = Course::all();
        $options = Option::all();
        $merchandises = Merchandise::all();
        $users = User::all();

        // editビューにデータを渡して表示
        return view('jobs.customers.edit', compact('customer', 'users','hairstyles', 'courses', 'options', 'merchandises'));
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
        // 指定されたIDの顧客データを取得
        $customer = Customers::findOrFail($id);

        // フォームからの入力を検証
        $validatedData = $request->validate([
            'name' => 'required|string',
            'phone_number' => 'required|string',
            'features' => 'nullable|string',
            'hairstyle_id' => 'nullable|exists:hairstyles,id',
            'course_id' => 'nullable|exists:courses,id',
            'option_id' => 'nullable|exists:options,id',
            'user_id' => 'nullable|exists:users,id',
            'merchandise_id' => 'nullable|exists:merchandises,id',
        ]);

        // 顧客データを更新
        $customer->update($validatedData);

        // index画面にリダイレクト
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
    
            // 指定されたIDの顧客データを取得
            $customer = Customers::findOrFail($id);
        
            // データを削除
            $customer->delete();
        
            // 顧客一覧ページにリダイレクト
            return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
