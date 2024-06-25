<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\customers;
use App\Models\course_customers;
use App\Models\option_customers;
use App\Models\merchandise_customers;
use App\Models\hairstyle_customers;
use App\Models\customer_users;
use App\Models\courses;
use App\Models\options;
use App\Models\merchandises;
use App\Models\hairstyles;
use App\Models\User;
use App\Models\users;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use App\Models\owner;
use App\Models\staff;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomersController extends Controller
{

    public function index($id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {
                // 顧客データを取得
                $customers = customers::where('owner_id', $id)->get();

                $courses = courses::where('owner_id', $id)->get();

                $options =
                    options::where('owner_id', $id)->get();

                $merchandises =
                    merchandises::where('owner_id', $id)->get();

                $hairstyles =
                    hairstyles::where('owner_id', $id)->get();


                $staff = staff::where('owner_id', $id)->pluck('user_id');
                Log::info('staff', $staff->toArray());

                if ($staff->isEmpty()) {
                    $owner = owner::where('user_id', $id)->first();
                    $users = User::find($owner->user_id);
                } else {
                    $owner = owner::where('user_id', $id)->first();
                    Log::info('owner', $owner->toArray());
                    $user = User::find($owner->user_id);
                    Log::info('user', $user->toArray());
                    $staff->push($user->id); // Eloquentコレクションに要素を追加
                    Log::info('staff', $staff->toArray());
                    $users = User::whereIn('id', $staff)->get();
                    Log::info('users', $users->toArray());
                }


                $responseUsers = $users->map(function ($user) {
                    return ['id' => $user->id, 'name' => $user->name];
                });

                $courseCustomer = course_customers::where('owner_id', $id)->get();

                $optionCustomer = option_customers::where('owner_id', $id)->get();

                $merchandiseCustomer = merchandise_customers::where('owner_id', $id)->get();

                $hairstyleCustomer = hairstyle_customers::where('owner_id', $id)->get();

                $userCustomer = customer_users::where('owner_id', $id)->get();

                if ($customers->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！新規作成ボタンから顧客を作成しましょう！",
                        'customers' => $customers,
                        'courses' => $courses,
                        'options' => $options,
                        'merchandises' => $merchandises,
                        'hairstyles' => $hairstyles,
                        'responseUsers' => $responseUsers,
                        'course_customers' => $courseCustomer,
                        'option_customers' => $optionCustomer,
                        'merchandise_customers' => $merchandiseCustomer,
                        'hairstyle_customers' => $hairstyleCustomer,
                        'customer_users' => $userCustomer,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'message' => '顧客情報を取得しました!',
                        'customers' => $customers,
                        'courses' => $courses,
                        'options' => $options,
                        'merchandises' => $merchandises,
                        'hairstyles' => $hairstyles,
                        'responseUsers' => $responseUsers,
                        'course_customers' => $courseCustomer,
                        'option_customers' => $optionCustomer,
                        'merchandise_customers' => $merchandiseCustomer,
                        'hairstyle_customers' => $hairstyleCustomer,
                        'customer_users' => $userCustomer,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限が！！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
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
                    'user_id' => 'required|array',
                    'user_id.*' => 'required|integer|exists:users,id',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                // 顧客を作成
                $customer = customers::create([
                    'customer_name' => $validatedData['customer_name'],
                    'phone_number' => $validatedData['phone_number'],
                    'remarks' => $validatedData['remarks'],
                    'owner_id' => $validatedData['owner_id'],
                ]);



                // 中間テーブルにデータを挿入
                $courseIds = $validatedData['courses_id'];
                $optionIds = $validatedData['options_id'];
                $merchandiseIds = $validatedData['merchandises_id'];
                $hairstyleIds = $validatedData['hairstyles_id'];
                $userIds = $validatedData['user_id'];

                $pivotData = [];
                foreach ($courseIds as $courseId) {
                    $pivotData[$courseId] = ['owner_id' => $validatedData['owner_id']];
                }

                $customer->courses()->sync($pivotData);

                $pivotData = [];
                foreach ($optionIds as $optionId) {
                    $pivotData[$optionId] = ['owner_id' => $validatedData['owner_id']];
                }

                $customer->options()->sync($pivotData);

                $pivotData = [];
                foreach ($merchandiseIds as $merchandiseId) {
                    $pivotData[$merchandiseId] = ['owner_id' => $validatedData['owner_id']];
                }

                $customer->merchandises()->sync($pivotData);

                $pivotData = [];
                foreach ($hairstyleIds as $hairstyleId) {
                    $pivotData[$hairstyleId] = ['owner_id' => $validatedData['owner_id']];
                }

                $customer->hairstyles()->sync($pivotData);

                $pivotData = [];
                foreach ($userIds as $userId) {
                    $pivotData[$userId] = ['owner_id' => $validatedData['owner_id']];
                }
                $customer->users()->sync($pivotData);


                $courseCustomer = course_customers::where('owner_id', $validatedData['owner_id'])->get();

                $optionCustomer = option_customers::where('owner_id', $validatedData['owner_id'])->get();

                $merchandiseCustomer = merchandise_customers::where('owner_id', $validatedData['owner_id'])->get();

                $hairstyleCustomer = hairstyle_customers::where('owner_id', $validatedData['owner_id'])->get();

                $userCustomer = customer_users::where('owner_id', $validatedData['owner_id'])->get();

                return
                    response()->json(
                        [
                            "customer" => $customer,
                            "course_customers" => $courseCustomer,
                            "option_customers" => $optionCustomer,
                            "merchandise_customers" => $merchandiseCustomer,
                            "hairstyle_customers" => $hairstyleCustomer,
                            "customer_users" => $userCustomer,
                        ],
                        200,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限が！！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    // public function show($id)
    // {
    //     try {
    //         // 指定されたIDの顧客データを取得
    //         $customer = customers::findOrFail($id);

    //         // showビューにデータを渡して表示
    //         return
    //             response()->json([
    //                 'customer' => $customer
    //             ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "message" => "顧客情報取得時にエラーが発生しました！"
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }


    public function update(Request $request)
    {

        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
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
                    'user_id' => 'required|array',
                    'user_id.*' => 'required|integer|exists:users,id',
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
                $userIds = $validatedData['user_id'];


                $pivotData = [];
                foreach ($courseIds as $courseId) {
                    $pivotData[$courseId] = ['owner_id' => $validatedData['owner_id']];
                }

                $customer->courses()->sync($pivotData);

                $pivotData = [];
                foreach ($optionIds as $optionId) {
                    $pivotData[$optionId] = ['owner_id' => $validatedData['owner_id']];
                }

                $customer->options()->sync($pivotData);

                $pivotData = [];
                foreach ($merchandiseIds as $merchandiseId) {
                    $pivotData[$merchandiseId] = ['owner_id' => $validatedData['owner_id']];
                }

                $customer->merchandises()->sync($pivotData);

                $pivotData = [];
                foreach ($hairstyleIds as $hairstyleId) {
                    $pivotData[$hairstyleId] = ['owner_id' => $validatedData['owner_id']];
                }

                $customer->hairstyles()->sync($pivotData);

                $pivotData = [];
                foreach ($userIds as $userId) {
                    $pivotData[$userId] = ['owner_id' => $validatedData['owner_id']];
                }
                $customer->users()->sync($pivotData);


                $courseCustomer = course_customers::where('owner_id', $validatedData['owner_id'])->get();

                $optionCustomer = option_customers::where('owner_id', $validatedData['owner_id'])->get();

                $merchandiseCustomer = merchandise_customers::where('owner_id', $validatedData['owner_id'])->get();

                $hairstyleCustomer = hairstyle_customers::where('owner_id', $validatedData['owner_id'])->get();

                $userCustomer = customer_users::where('owner_id', $validatedData['owner_id'])->get();


                return
                    response()->json([
                        "customer" =>  $customer,
                        "course_customers" => $courseCustomer,
                        "option_customers" => $optionCustomer,
                        "merchandise_customers" => $merchandiseCustomer,
                        "hairstyle_customers" => $hairstyleCustomer,
                        "customer_users" => $userCustomer,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限が！！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => '顧客情報更新時にエラーが発生しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                // 指定されたIDの顧客データを取得
                $customer = customers::find($request->id);
                if (!$customer) {
                    return response()->json([
                        'message' =>
                        '顧客が見つかりません！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
                // 顧客データを削除
                $customer->delete();
                return response()->json([
                    "deleteId"  => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限が！！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "顧客情報削除時にエラーが発生しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
