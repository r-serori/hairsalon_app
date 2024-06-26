<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Course;
use App\Models\Option;
use App\Models\Merchandise;
use App\Models\Hairstyle;
use App\Models\Staff;
use App\Models\Owner;
use App\Models\User;
use App\Models\CourseCustomer;
use App\Models\OptionCustomer;
use App\Models\MerchandiseCustomer;
use App\Models\HairstyleCustomer;
use App\Models\CustomerUser;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomersController extends Controller
{

    public function index($owner_id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {
                // 顧客データを取得
                $decodedOwnerId = urldecode($owner_id);

                $customers = Customer::where('owner_id', $decodedOwnerId)->get();

                $courses = Course::where('owner_id', $decodedOwnerId)->get();

                $options =
                    Option::where('owner_id', $decodedOwnerId)->get();

                $merchandises =
                    Merchandise::where('owner_id', $decodedOwnerId)->get();

                $hairstyles =
                    Hairstyle::where('owner_id', $decodedOwnerId)->get();


                $staffs = Staff::where('owner_id', $decodedOwnerId)->pluck('user_id');
                // Log::info('staff', $staff->toArray());

                if ($staffs->isEmpty()) {
                    $owner = Owner::find($decodedOwnerId);
                    $users = User::find($owner->user_id);
                } else {
                    $owner = Owner::find($decodedOwnerId);
                    // Log::info('owner', $owner->toArray());
                    $OwnersUser = User::find($owner->user_id);
                    // Log::info('user', $user->toArray());
                    $staffs->push($OwnersUser->id);
                    // Log::info('staff', $staff->toArray());
                    $users = User::whereIn('id', $staffs)->get();
                    // Log::info('users', $users->toArray());
                }


                $responseUsers = $users->map(function ($user) {
                    return ['id' => $user->id, 'name' => $user->name];
                });

                $courseCustomer = CourseCustomer::where('owner_id', $decodedOwnerId)->get();

                $optionCustomer = OptionCustomer::where('owner_id', $decodedOwnerId)->get();

                $merchandiseCustomer = MerchandiseCustomer::where('owner_id', $decodedOwnerId)->get();

                $hairstyleCustomer = HairstyleCustomer::where('owner_id', $decodedOwnerId)->get();

                $userCustomer = CustomerUser::where('owner_id', $decodedOwnerId)->get();

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
                    'course_id' => 'required|array',
                    'course_id.*' => 'required|integer|exists:courses,id',
                    'option_id' => 'required|array',
                    'option_id.*' => 'required|integer|exists:options,id',
                    'merchandise_id' => 'required|array',
                    'merchandise_id.*' => 'required|integer|exists:merchandises,id',
                    'hairstyle_id' => 'required|array',
                    'hairstyle_id.*' => 'required|integer|exists:hairstyles,id',
                    'user_id' => 'required|array',
                    'user_id.*' => 'required|integer|exists:users,id',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                // 顧客を作成
                $customer = Customer::create([
                    'customer_name' => $validatedData['customer_name'],
                    'phone_number' => $validatedData['phone_number'],
                    'remarks' => $validatedData['remarks'],
                    'owner_id' => $validatedData['owner_id'],
                ]);



                // 中間テーブルにデータを挿入
                $courseIds = $validatedData['course_id'];
                $optionIds = $validatedData['option_id'];
                $merchandiseIds = $validatedData['merchandise_id'];
                $hairstyleIds = $validatedData['hairstyle_id'];
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


                $courseCustomer = CourseCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $optionCustomer = OptionCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $merchandiseCustomer = MerchandiseCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $hairstyleCustomer = HairstyleCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $userCustomer = CustomerUser::where('owner_id', $validatedData['owner_id'])->get();

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
    //         $customer = Customer::findOrFail($id);

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
                    'id' => 'required|integer|exists:customers,id',
                    'customer_name' => 'required',
                    'phone_number' => 'nullable',
                    'remarks' => 'nullable',
                    'course_id' => 'required|array',
                    'course_id.*' => 'required|integer|exists:courses,id',
                    'option_id' => 'required|array',
                    'option_id.*' => 'required|integer|exists:options,id',
                    'merchandise_id' => 'required|array',
                    'merchandise_id.*' => 'required|integer|exists:merchandises,id',
                    'hairstyle_id' => 'required|array',
                    'hairstyle_id.*' => 'required|integer|exists:hairstyles,id',
                    'user_id' => 'required|array',
                    'user_id.*' => 'required|integer|exists:users,id',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                // 指定されたIDの顧客データを取得
                $customer = Customer::find($request->id);

                // 顧客データを更新
                $customer->customer_name = $validatedData['customer_name'];
                $customer->phone_number = $validatedData['phone_number'];
                $customer->remarks = $validatedData['remarks'];

                $customer->save();

                // 中間テーブルにデータを挿入
                $courseIds = $validatedData['course_id'];
                $optionIds = $validatedData['option_id'];
                $merchandiseIds = $validatedData['merchandise_id'];
                $hairstyleIds = $validatedData['hairstyle_id'];
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


                $courseCustomer = CourseCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $optionCustomer = OptionCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $merchandiseCustomer = MerchandiseCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $hairstyleCustomer = HairstyleCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $userCustomer = CustomerUser::where('owner_id', $validatedData['owner_id'])->get();


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
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
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
                $customer = Customer::find($request->id);
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
