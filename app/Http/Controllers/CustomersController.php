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
            'courses_id' => 'required|array',
            'courses_id.*' => 'required|integer|exists:courses,id',
            'options_id' => 'required|array',
            'options_id.*' => 'required|integer|exists:options,id',
            'merchandises_id' => 'required|array',
            'merchandises_id.*' => 'required|integer|exists:merchandises,id',
            'hairstyles_id' => 'required|array',
            'hairstyles_id.*' => 'required|integer|exists:hairstyles,id',
            'attendances_id' => 'required|array',
            'attendances_id.*' => 'required|integer|exists:attendances,id',
        ]);

        // 顧客を作成
        $customer = customers::create([
            'customer_name' => $validatedData['customer_name'],
            'phone_number' => $validatedData['phone_number'],
            'remarks' => $validatedData['remarks'],
        ]);



        // 中間テーブルにデータを挿入
        $courseIds = $validatedData['courses_id'];
        $optionIds = $validatedData['options_id'];
        $merchandiseIds = $validatedData['merchandises_id'];
        $hairstyleIds = $validatedData['hairstyles_id'];
        $attendanceIds = $validatedData['attendances_id'];

        $customer->courses()->sync($courseIds);
        $customer->options()->sync($optionIds);
        $customer->merchandises()->sync($merchandiseIds);
        $customer->hairstyles()->sync($hairstyleIds);
        $customer->attendances()->sync($attendanceIds);



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
            'courses_id' => 'required|array',
            'courses_id.*' => 'required|integer|exists:courses,id',
            'options_id' => 'required|array',
            'options_id.*' => 'required|integer|exists:options,id',
            'merchandises_id' => 'required|array',
            'merchandises_id.*' => 'required|integer|exists:merchandises,id',
            'hairstyles_id' => 'required|array',
            'hairstyles_id.*' => 'required|integer|exists:hairstyles,id',
            'attendances_id' => 'required|array',
            'attendances_id.*' => 'required|integer|exists:attendances,id',
        ]);

        // 指定されたIDの顧客データを取得
        $customer = customers::findOrFail($id);

        // 顧客データを更新
        $customer->customer_name = $validatedData['customer_name'];
        $customer->phone_number = $validatedData['phone_number'];
        $customer->remarks = $validatedData['remarks'];

        // 中間テーブルにデータを挿入
        $courseIds = $validatedData['courses_id'];
        $optionIds = $validatedData['options_id'];
        $merchandiseIds = $validatedData['merchandises_id'];
        $hairstyleIds = $validatedData['hairstyles_id'];
        $attendanceIds = $validatedData['attendances_id'];

        $customer->courses()->sync($courseIds);
        $customer->options()->sync($optionIds);
        $customer->merchandises()->sync($merchandiseIds);
        $customer->hairstyles()->sync($hairstyleIds);
        $customer->attendances()->sync($attendanceIds);

        $customer->save();


        return
            response()->json([], 204);
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
