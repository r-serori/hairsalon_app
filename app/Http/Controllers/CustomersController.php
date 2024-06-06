<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customers;
use App\Models\course_customers;
use App\Models\option_customers;
use App\Models\merchandise_customers;
use App\Models\hairstyle_customers;
use App\Models\customer_attendances;
use App\Models\courses;
use App\Models\options;
use App\Models\merchandises;
use App\Models\hairstyles;
use App\Models\attendances;


class CustomersController extends Controller
{

    public function index()
    {
        try {
            // 顧客データを取得
            $customers = customers::all(); // または適切なクエリを使用してデータを取得する

            $courses = courses::all();

            $options = options::all();

            $merchandises = merchandises::all();

            $hairstyles = hairstyles::all();

            $attendances = attendances::all();

            $courseCustomer = course_customers::all();

            $optionCustomer = option_customers::all();

            $merchandiseCustomer = merchandise_customers::all();

            $hairstyleCustomer = hairstyle_customers::all();

            $attendanceCustomer = customer_attendances::all();

            if ($customers->isEmpty()) {
                return response()->json([
                    "resStatus" => "success",
                    "message" => "初めまして！新規作成ボタンから顧客を作成しましょう！",
                    'customers' => $customers,
                    'courses' => $courses,
                    'options' => $options,
                    'merchandises' => $merchandises,
                    'hairstyles' => $hairstyles,
                    'attendances' => $attendances,
                    'course_customers' => $courseCustomer,
                    'option_customers' => $optionCustomer,
                    'merchandise_customers' => $merchandiseCustomer,
                    'hairstyle_customers' => $hairstyleCustomer,
                    'customer_attendances' => $attendanceCustomer,
                ], 200);
            } else {
                return
                    response()->json([
                        "resStatus" => "success",
                        'customers' => $customers,
                        'courses' => $courses,
                        'options' => $options,
                        'merchandises' => $merchandises,
                        'hairstyles' => $hairstyles,
                        'attendances' => $attendances,
                        'course_customers' => $courseCustomer,
                        'option_customers' => $optionCustomer,
                        'merchandise_customers' => $merchandiseCustomer,
                        'hairstyle_customers' => $hairstyleCustomer,
                        'customer_attendances' => $attendanceCustomer,
                    ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "顧客情報取得時にエラーが発生しました。"
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
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

            $courseCustomer = course_customers::where('customers_id', $customer->id)->get();

            $optionCustomer = option_customers::where('customers_id', $customer->id)->get();

            $merchandiseCustomer = merchandise_customers::where('customers_id', $customer->id)->get();

            $hairstyleCustomer = hairstyle_customers::where('customers_id', $customer->id)->get();

            $attendanceCustomer = customer_attendances::where('customers_id', $customer->id)->get();


            return
                response()->json(
                    [
                        "resStatus" => "success",
                        "customer" => $customer,
                        "course_customers" => $courseCustomer,
                        "option_customers" => $optionCustomer,
                        "merchandise_customers" => $merchandiseCustomer,
                        "hairstyle_customers" => $hairstyleCustomer,
                        "customer_attendances" => $attendanceCustomer,
                    ],
                    200
                );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "顧客情報登録時にエラーが発生しました。"
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            // 指定されたIDの顧客データを取得
            $customer = customers::findOrFail($id);

            // showビューにデータを渡して表示
            return
                response()->json([
                    'customer' => $customer
                ]);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "顧客情報取得時にエラーが発生しました。"
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {

        try {
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
            $customer = customers::find($request->id);

            // 顧客データを更新
            $customer->customer_name = $validatedData['customer_name'];
            $customer->phone_number = $validatedData['phone_number'];
            $customer->remarks = $validatedData['remarks'];

            $customer->save();


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

            $courseCustomer = course_customers::where('customers_id', $customer->id)->get();

            $optionCustomer = option_customers::where('customers_id', $customer->id)->get();

            $merchandiseCustomer = merchandise_customers::where('customers_id', $customer->id)->get();

            $hairstyleCustomer = hairstyle_customers::where('customers_id', $customer->id)->get();

            $attendanceCustomer = customer_attendances::where('customers_id', $customer->id)->get();



            return
                response()->json([
                    "resStatus" => "success",
                    "customer" =>  $customer,
                    "course_customers" => $courseCustomer,
                    "option_customers" => $optionCustomer,
                    "merchandise_customers" => $merchandiseCustomer,
                    "hairstyle_customers" => $hairstyleCustomer,
                    "customer_attendances" => $attendanceCustomer,
                ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function destroy($id)
    {
        try {
            // 指定されたIDの顧客データを取得
            $customer = customers::find($id);
            if (!$customer) {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    '顧客が見つかりません。'
                ], 500);
            }
            // 顧客データを削除
            $customer->delete();
            return response()->json([
                "resStatus" => "success",
                "deleteId"  => $id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "顧客情報削除時にエラーが発生しました。"
            ], 500);
        }
    }
}
