<?php

namespace App\Http\Controllers;

use App\Models\attendances;
use Illuminate\Http\Request;
use App\Models\customers;
use App\Models\hairstyles;
use App\Models\courses;
use App\Models\customer_prices;
use App\Models\options;
use App\Models\merchandises;


class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 検索キーワードを取得
        $search = $request->input('search');

        // 検索がある場合は顧客名で部分一致検索を行う
        if ($search) {
            $customers = customers::where('customer_name', 'like', '%' . $search . '%')
                ->with(['attendances', 'hairstyles', 'courses', 'options', 'merchandises'])
                ->paginate(20);
        } else {
            // 検索がない場合は全ての顧客を取得する
            //withメソッドはmodelsのメソッドから参照されている、中間テーブルのデータを取得する
            $customers = customers::with(['attendances', 'hairstyles', 'courses', 'options', 'merchandises'])
                ->paginate(20);
        }

        // 中間テーブルを含むデータをビューに渡す
        return view('jobs.customers.index', compact('customers'));
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hairstyles = hairstyles::all();
        $courses = courses::all();
        $options = options::all();
        $merchandises = merchandises::all();
        $attendances = attendances::all();



        return view('jobs.customers.create', compact('attendances', 'hairstyles', 'courses', 'options', 'merchandises'));
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
            'phone_number' => 'required',
            'remarks' => 'nullable',
            'new_customer' => 'required', // 新規or既存の選択肢はフォームから受け取らないので、ここで代入
            'courses_id' => 'array', // 複数選択されたコースのIDの配列を受け取る
            'courses_id.*' => 'nullable|exists:courses,id', // 選択された全てのコースが存在することを確認
            'options_id' => 'array',
            'options_id.*' => 'nullable|exists:options,id',
            'merchandises_id' => 'array',
            'merchandises_id.*' => 'nullable|exists:merchandises,id',
            'hairstyles_id' => 'array',
            'hairstyles_id.*' => 'nullable|exists:hairstyles,id',
            'attendances_id' => 'nullable|exists:attendances,id',
        ]);

        // 顧客を作成
        $customer = customers::create([
            'customer_name' => $validatedData['customer_name'],
            'phone_number' => $validatedData['phone_number'],
            'remarks' => $validatedData['remarks'],
            'new_customer' => $validatedData['new_customer'], // 新規or既存の選択肢はフォームから受け取らないので、ここで代入
        ]);


        // 顧客とコースの中間テーブルにデータを保存
        if (isset($validatedData['courses_id'])) {
            $customer->courses()->attach($validatedData['courses_id']);
            $courses = courses::find($validatedData['courses_id']);
        }

        if (isset($validatedData['options_id'])) {
            $customer->options()->attach($validatedData['options_id']);
            $options = options::find($validatedData['options_id']);
            }
        

        if (isset($validatedData['merchandises_id'])) {
            $customer->merchandises()->attach($validatedData['merchandises_id']);
            $merchandises = merchandises::find($validatedData['merchandises_id']);
            }
        

        if (isset($validatedData['hairstyles_id'])) {
            $customer->hairstyles()->attach($validatedData['hairstyles_id']);
        }
        if (isset($validatedData['attendances_id'])) {
            $customer->attendances()->attach($validatedData['attendances_id']);
        }


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
        $hairstyles = hairstyles::all();
        $courses = courses::all();
        $options = options::all();
        $merchandises = merchandises::all();
        $attendances = attendances::all();

        // editビューにデータを渡して表示
        return view('jobs.customers.edit', compact('customer', 'attendances', 'hairstyles', 'courses', 'options', 'merchandises'));
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
            'phone_number' => 'required',
            'remarks' => 'nullable',
            'new_customer' => 'required', // 新規or既存の選択肢はフォームから受け取らないので、ここで代入
            'courses_id' => 'array', // 複数選択されたコースのIDの配列を受け取る
            'courses_id.*' => 'nullable|exists:courses,id', // 選択された全てのコースが存在することを確認
            'options_id' => 'array',
            'options_id.*' => 'nullable|exists:options,id',
            'merchandises_id' => 'array',
            'merchandises_id.*' => 'nullable|exists:merchandises,id',
            'hairstyles_id' => 'array',
            'hairstyles_id.*' => 'nullable|exists:hairstyles,id',
            'attendances_id' => 'nullable|exists:attendances,id',
        ]);

        // 顧客データを更新
        $customer->update([
            'customer_name' => $validatedData['customer_name'],
            'phone_number' => $validatedData['phone_number'],
            'remarks' => $validatedData['remarks'],
            'new_customer' => $validatedData['new_customer'],
        ]);

        // 中間テーブルのデータを一度削除
        $customer->courses()->detach();
        $customer->options()->detach();
        $customer->merchandises()->detach();
        $customer->hairstyles()->detach();
        $customer->attendances()->detach();

        // 新しいデータを中間テーブルに追加
        if (isset($validatedData['courses_id'])) {
            $customer->courses()->attach($validatedData['courses_id']);
        }
        if (isset($validatedData['options_id'])) {
            $customer->options()->attach($validatedData['options_id']);
        }
        if (isset($validatedData['merchandises_id'])) {
            $customer->merchandises()->attach($validatedData['merchandises_id']);
        }
        if (isset($validatedData['hairstyles_id'])) {
            $customer->hairstyles()->attach($validatedData['hairstyles_id']);
        }
        if (isset($validatedData['attendances_id'])) {
            $customer->attendances()->attach($validatedData['attendances_id']);
        }

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

        // hairstyle_customers テーブルから関連するレコードを削除
        $customer->hairstyles()->detach();



        // 顧客データを削除
        $customer->delete();

        // 顧客一覧ページにリダイレクト
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
