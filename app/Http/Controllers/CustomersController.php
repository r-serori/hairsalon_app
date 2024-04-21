<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customers;


class CustomersController extends Controller
{

    public function index()
    {

        // 顧客データを取得
        $customers = customers::all(); // または適切なクエリを使用してデータを取得する


        // 中間テーブルを含むデータをビューに渡す
        return
            response()->json(['customers' => $customers]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_name' => 'required',
            'phone_number' => 'nullable',
            'remarks' => 'nullable',
            'new_customer' => 'required',
        ]);

        // 顧客を作成
        $customer = customers::create([
            'customer_name' => $validatedData['customer_name'],
            'phone_number' => $validatedData['phone_number'],
            'remarks' => $validatedData['remarks'],
            'new_customer' => $validatedData['new_customer'],
        ]);

        return
            response()->json(
                [],
                204
            );
    }


    public function show($id)
    {
        // 指定されたIDの顧客データを取得
        $customer = customers::findOrFail($id);

        // showビューにデータを渡して表示
        return
            response()->json(['customer' => $customer]);
    }


    public function update(Request $request, $id)
    {


        $validatedData = $request->validate([
            'customer_name' => 'required',
            'phone_number' => 'nullable',
            'remarks' => 'nullable',
            'new_customer' => 'required',
        ]);

        // 指定されたIDの顧客データを取得
        $customer = customers::findOrFail($id);

        // 顧客データを更新
        $customer->customer_name = $validatedData['customer_name'];
        $customer->phone_number = $validatedData['phone_number'];
        $customer->remarks = $validatedData['remarks'];
        $customer->new_customer = $validatedData['new_customer'];

        $customer->save();

        return
            response()->json(
                [],
                204
            );
    }

    public function destroy($id)
    {
        // 指定されたIDの顧客データを取得
        $customer = customers::findOrFail($id);
        if (!$customer) {
            return response()->json(['message' =>
            'customer not found'], 404);
        }

        try {
            // 顧客データを削除
            $customer->delete();
            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
