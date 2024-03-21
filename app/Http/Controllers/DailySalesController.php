<?php

namespace App\Http\Controllers;

use App\Models\daily_sales;
use App\Models\monthly_sales;
use Illuminate\Http\Request;

class DailySalesController extends Controller
{
    public function index()
    {
        $daily_sales = daily_sales::all()->sortBy('date');


        return view('stores.daily_sales.index', compact('daily_sales'));
    }

    public function create()
    {
        return view('stores.daily_sales.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'daily_sales' => 'required',
        ]);

        daily_sales::create([
            'date' => $validatedData['date'],
            'daily_sales' => $validatedData['daily_sales'],
        ]);

        return redirect()->route('daily_sales.index');
    }

    public function show($id)
    {
        $dailySales = daily_sales::find($id);
        return view('stores.daily_sales.show', compact('dailySales'));
    }

    public function edit($id)
    {
        $dailySales = daily_sales::find($id);
        return view('stores.daily_sales.edit', compact('dailySales'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'daily_sales' => 'required',
        ]);

        $dailySales = daily_sales::find($id);

        $dailySales->date = $validatedData['date'];
        $dailySales->daily_sales = $validatedData['daily_sales'];

        $dailySales->save();
        return redirect()->route('daily_sales.index');
    }

    public function destroy($id)
    {
        $dailySales = daily_sales::find($id);
        $dailySales->delete();
        return redirect()->route('daily_sales.index');
    }



    public function updateMonthlySales(Request $request)
    {


        // バリデーション
        $validatedData = $request->validate([

            'year' => 'required',
            'month' => 'required',
        ]);

        // 月次売り上げの取得
        $monthlySalesAmount = daily_sales::whereYear('date', $validatedData['year'])
            ->whereMonth('date', $validatedData['month'])
            ->sum('daily_sales');


        // 指定された年月のレコードを monthly_sales テーブルから取得
        $monthlySales = monthly_sales::where('year', $validatedData['year'])
            ->where('month', $validatedData['month'])
            ->first();

        if ($monthlySales) {
            // レコードが存在する場合、更新処理を行う
            $monthlySales->monthly_sales = $monthlySalesAmount;
            $monthlySales->save();
            return redirect()->route('daily_sales.index')->with('success', '月次売り上げを更新しました');
        } else {
            // レコードが存在しない場合、新規作成処理を行う
            monthly_sales::create([
                'year' => $validatedData['year'],
                'month' => $validatedData['month'],
                'monthly_sales' => $monthlySalesAmount,

            ]);
            // リダイレクトおよび成功メッセージの返却
        return redirect()->route('daily_sales.index')->with('success', '月次売り上げを作成しました');
        }
        
    }
}
