<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\monthly_sales;
use App\Models\yearly_sales;


class MonthlySalesController extends Controller
{
    public function index()
    {

        // 月別売上一覧を取得
        $monthly_sales = monthly_sales::all();

        // 月別売上一覧ページにデータを渡して表示
        return response()->json(['monthly_sales' => $monthly_sales]);
    }

    public function store(Request $request)
    {
        // バリデーションルールを定義する
        $validatedData = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer',
            'monthly_sales' => 'required|integer',
        ]);

        // 月別売上モデルを作成して保存する

        monthly_sales::create([
            'year' => $validatedData['year'],
            'month' => $validatedData['month'],
            'monthly_sales' => $validatedData['monthly_sales'],
        ]);

        // 成功したらリダイレクト
        return response()->json([], 204);
    }


    public function show($id)
    {
        // 指定されたIDの月別売上を取得
        $monthly_sale = monthly_sales::find($id);

        // 月別売上を表示
        return response()->json(['monthly_sale' => $monthly_sale]);
    }



    public function update(Request $request, $id)
    {
        // バリデーションルールを定義する
        $validatedData = $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer',
            'monthly_sales' => 'required|integer',
        ]);

        // 月別売上を取得する
        $monthly_sale = monthly_sales::findOrFail($id);

        // 月別売上を更新する
        $monthly_sale->year = $validatedData['year'];
        $monthly_sale->month = $validatedData['month'];
        $monthly_sale->monthly_sales = $validatedData['monthly_sales'];
        $monthly_sale->save();

        // 成功したらリダイレクト
        return response()->json(
            [],
            204
        );
    }

    public function destroy($id)
    {
        $monthly_sale = monthly_sales::find($id);
        if (!$monthly_sale) {
            return response()->json(['message' =>
            'monthly_sale not found'], 404);
        }

        try {
            $monthly_sale->delete();
            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json(['message' =>
            'monthly_sale has child records'], 409);
        }
    }
}
