<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\monthly_sales;
use App\Models\yearly_sales;


class MonthlySalesController extends Controller
{
    public function index()
    {
        $monthly_sales = monthly_sales::all()->sortBy('year')->sortBy('month');


        return view('stores.monthly_sales.index', compact('monthly_sales'));
    }

    public function create()
    {
        return view('stores.monthly_sales.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'year' => 'required',
            'month' => 'required',
            'monthly_sales' => 'required',
        ]);

        monthly_sales::create([
            'year' => $validatedData['year'],
            'month' => $validatedData['month'],
            'monthly_sales' => $validatedData['monthly_sales'],
        ]);
        return redirect()->route('monthly_sales.index');
    }

    public function show($id)
    {
        $monthlySales = monthly_sales::find($id);
        return view('stores.monthly_sales.show', compact('monthlySales'));
    }

    public function edit($id)
    {
        $monthlySales = monthly_sales::find($id);
        return view('stores.monthly_sales.edit', compact('monthlySales'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'year' => 'required',
            'month' => 'required',
            'monthly_sales' => 'required',
        ]);

        $monthlySales = monthly_sales::find($id);

        $monthlySales->year = $validatedData['year'];
        $monthlySales->month = $validatedData['month'];
        $monthlySales->monthly_sales = $validatedData['monthly_sales'];

        $monthlySales->save();
        return redirect()->route('monthly_sales.index');
    }

    public function destroy($id)
    {
        $monthlySales = monthly_sales::find($id);
        $monthlySales->delete();
        return redirect()->route('monthly_sales.index');
    }

    public function updateYearlySales(Request $request)
    {
        // バリデーション
        $validatedData = $request->validate([
            'year' => 'required',
        ]);

        // 月次売り上げの年別売り上げを取得
        $yearlySalesAmount = monthly_sales::where('year', $validatedData['year'])
            ->sum('monthly_sales');

        // 年次売り上げのレコードを取得
        $yearlySales = yearly_sales::where('year', $validatedData['year'])->first();

        // 年次売り上げのレコードが存在しない場合は新規作成
        if ($yearlySales) {
            // 年次売り上げのレコードが存在する場合は更新
            $yearlySales->yearly_sales = $yearlySalesAmount;
            $yearlySales->save();
            return redirect()->route('monthly_sales.index')->with('success', '年次売り上げを更新しました');
        } else {
            yearly_sales::create([
                'year' => $validatedData['year'],
                'yearly_sales' => $yearlySalesAmount,
            ]);
        // リダイレクトおよび成功メッセージの返却
        return redirect()->route('monthly_sales.index')->with('success', '年次売り上げを作成しました');
    

    }
}
}