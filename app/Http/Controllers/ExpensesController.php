<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\expenses;
use App\Models\expense_categories;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {

        // カテゴリー一覧を取得
        $expense_categories = expense_categories::all();

        // カテゴリーIDと商品名を取得
        $categoryId = $request->input('category');

        // 在庫データを取得するクエリを作成
        $query = expenses::query()->with('expense_category');

        // カテゴリーでフィルタリング
        if ($categoryId) {
            $query->where('expense_category_id', $categoryId);
        }

        // 在庫データを取得
        $expenses = $query->get();



        // 在庫一覧ページにデータを渡して表示
        return view('stores.expenses.index', compact('expenses', 'expense_categories'));
    }

    public function search(Request $request, $id)
    {
        // 検索フォームから入力された年月を取得
        $searchDate = $request->input('search_date');

        // 年月が入力されている場合、年と月に分割
        $searchYear = null;
        $searchMonth = null;
        if ($searchDate) {
            list($searchYear, $searchMonth) = explode('-', $searchDate);
        }

        // 出席時間データを取得するクエリを実行
        $query = expenses::all();

        // 年の検索条件を追加
        if ($searchYear) {
            $query->whereYear('date', $searchYear);
        }

        // 月の検索条件を追加
        if ($searchMonth) {
            $query->whereMonth('date', $searchMonth);
        }

        dd($query);


        // カテゴリー一覧を取得
        $expense_categories = expense_categories::all();


        // 出席時間データを取得
        $expenses = $query->orderBy('date', 'desc')->get();


        // 検索結果を表示するビューを返す
        return view('stores.expenses.search_result', compact('expenses', 'searchYear', 'searchMonth'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $expense_categories = \App\Models\expense_categories::all();
        return view('stores.expenses.create', compact('expense_categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // バリデーションルールを定義する
        $validatedData = $request->validate([
            'expense_name' => 'required|string',
            'expense_location' => 'required|nullable|string',
            'date' => 'required|date',
            'remarks' => 'required|nullable|string',
            'expense_price' => 'required|integer',
            'expense_category_id' => 'required|exists:expense_categories,id',
        ]);


        // 経費モデルを作成して保存する
        expenses::create([
            'expense_name' => $validatedData['expense_name'],
            'expense_location' => $validatedData['expense_location'],
            'date' => $validatedData['date'],
            'remarks' => $validatedData['remarks'],
            'expense_price' => $validatedData['expense_price'],
            'expense_category_id' => $validatedData['expense_category_id'],
        ]);

        return redirect()->route('expenses.index')->with('success', '経費の新規作成に成功しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {

        // 検索フォームから入力された年月を取得
        $searchDate = $request->input('search_date');

        // 年月が入力されている場合、年と月に分割
        $searchYear = null;
        $searchMonth = null;
        if ($searchDate) {
            list($searchYear, $searchMonth) = explode('-', $searchDate);
        }

        // 出席時間データを取得するクエリを実行
        $query = expenses::query();

        // 年の検索条件を追加
        if ($searchYear) {
            $query->whereYear('date', $searchYear);
        }

        // 月の検索条件を追加
        if ($searchMonth) {
            $query->whereMonth('date', $searchMonth);
        }


        // カテゴリー一覧を取得
        $expense_categories = expense_categories::all();


        // 出席時間データを取得
        $expenses = $query->orderBy('date', 'desc')->get();


        return view('stores.expenses.show', compact('expenses',  'searchYear', 'searchMonth', 'expense_categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $expense = expenses::find($id);
        $expense_categories = \App\Models\expense_categories::all();
        return view('stores.expenses.edit', compact('expense', 'expense_categories'));
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
        // バリデーションルールを定義する
        $validatedData = $request->validate([
            'expense_name' => 'required|string',
            'expense_location' => 'required|string',
            'date' => 'required|date',
            'remarks' => 'required|nullable|string',
            'expense_price' => 'required|integer',
            'expense_category_id' => 'required|exists:expense_categories,id',
        ]);


        // 経費を取得する
        $expense = expenses::findOrFail($id);

        // リクエストから受け取ったデータを使用してレコードを更新
        $expense->expense_name = $validatedData['expense_name'];
        $expense->expense_location = $validatedData['expense_location'];
        $expense->date = $validatedData['date'];
        $expense->remarks = $validatedData['remarks'];
        $expense->expense_price = $validatedData['expense_price'];
        $expense->expense_category_id = $validatedData['expense_category_id'];

        // レコードを保存
        $expense->save();




        return redirect()->route('expenses.index')->with('success', '経費の更新に成功しました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        expenses::destroy($id);
        return redirect()->route('expenses.index')->with('success', '経費を削除しました。');
    }
}
