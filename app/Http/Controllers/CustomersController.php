<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customers;


class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // 顧客データを取得
        $customers = customers::all(); // または適切なクエリを使用してデータを取得する


        // 中間テーブルを含むデータをビューに渡す
        return
            response()->json(['customers' => $customers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('jobs.customers.create');
    }

    public function scheduleCreate($id)
    {
        $customer = customers::findOrFail($id);



        return view(
            'jobs.customers.scheduleCreate'
        );
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
            'customer_name' => 'required',
            'phone_number' => 'nullable',
            'remarks' => 'nullable',
            'new_customer' => 'required', // 新規or既存の選択肢はフォームから受け取らないので、
        ]);

        // 顧客を作成
        $customer = customers::create([
            'customer_name' => $validatedData['customer_name'],
            'phone_number' => $validatedData['phone_number'],
            'remarks' => $validatedData['remarks'],
            'new_customer' => $validatedData['new_customer'], // 新規or既存の選択肢はフォームから受け取らないので、ここで代入
        ]);



        // index画面にリダイレクト
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
        $customer = customers::findOrFail($id);

        // showビューにデータを渡して表示
        return
            response()->json(['customer' => $customer]);
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


        // editビューにデータを渡して表示
        return view('jobs.customers.edit');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $customer = customers::findOrFail($id);

        $validatedData = $request->validate([
            'customer_name' => 'required',
            'phone_number' => 'nullable',
            'remarks' => 'nullable',
            'new_customer' => 'required',
        ]);

        // 顧客データを更新
        $customer->update([
            'customer_name' => $validatedData['customer_name'],
            'phone_number' => $validatedData['phone_number'],
            'remarks' => $validatedData['remarks'],
            'new_customer' => $validatedData['new_customer'],
        ]);


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
        $customer = customers::findOrFail($id);


        // 顧客データを削除
        $customer->delete();

        // 顧客一覧ページにリダイレクト
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
