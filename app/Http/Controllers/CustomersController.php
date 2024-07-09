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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CustomersController extends Controller
{

    public function index()
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {
                // 顧客データを取得

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $customersCacheKey = 'owner_' . $ownerId . 'customers';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $customers = Cache::remember($customersCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return  Customer::where('owner_id', $ownerId)->get();
                });

                $coursesCacheKey = 'owner_' . $ownerId . 'courses';


                $courses = Cache::remember($coursesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return  Course::where('owner_id', $ownerId)->get();
                });

                $optionsCacheKey = 'owner_' . $ownerId . 'options';

                $options = Cache::remember($optionsCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return  Option::where('owner_id', $ownerId)->get();
                });

                $merchandisesCacheKey = 'owner_' . $ownerId . 'merchandises';

                $merchandises = Cache::remember($merchandisesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return  Merchandise::where('owner_id', $ownerId)->get();
                });

                $hairstylesCacheKey = 'owner_' . $ownerId . 'hairstyles';

                $hairstyles = Cache::remember($hairstylesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return  Hairstyle::where('owner_id', $ownerId)->get();
                });

                $staffs = Staff::where('owner_id', $ownerId)->pluck('user_id');
                // Log::info('staff', $staff->toArray());

                if ($staffs->isEmpty()) {
                    $owner = Owner::find($ownerId);
                    $owUser = User::find($owner->user_id);
                    $responseUsers =
                        ['id' => $owUser->id, 'name' => $owUser->name];
                } else {
                    $owner = Owner::find($ownerId);
                    // Log::info('owner', $owner->toArray());
                    $OwnersUser = User::find($owner->user_id);
                    // Log::info('user', $user->toArray());
                    $staffs->push($OwnersUser->id);
                    // Log::info('staff', $staff->toArray());
                    $users = User::whereIn('id', $staffs)->get();
                    // Log::info('users', $users->toArray());
                    $responseUsers =
                        $users->map(function ($user) {
                            return ['id' => $user->id, 'name' => $user->name];
                        });
                }

                $courseCustomersCache = 'owner_' . $ownerId . 'course_customers';

                $courseCustomer = Cache::remember($courseCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  CourseCustomer::where('owner_id', $ownerId)->get();
                });

                $optionCustomersCache = 'owner_' . $ownerId . 'option_customers';

                $optionCustomer = Cache::remember($optionCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  OptionCustomer::where('owner_id', $ownerId)->get();
                });

                $merchandiseCustomersCache = 'owner_' . $ownerId . 'merchandise_customers';

                $merchandiseCustomer = Cache::remember($merchandiseCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  MerchandiseCustomer::where('owner_id', $ownerId)->get();
                });

                $hairstyleCustomersCache = 'owner_' . $ownerId . 'hairstyle_customers';

                $hairstyleCustomer = Cache::remember($hairstyleCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  HairstyleCustomer::where('owner_id', $ownerId)->get();
                });

                $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();

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
                    "message" => "あなたには権限がありません！"
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
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $validatedData = $request->validate([
                    'customer_name' => 'required|string',
                    'phone_number' => 'nullable',
                    'remarks' => 'nullable',
                    'course_id' => 'array|nullable',
                    'course_id.*' => 'nullable|integer|exists:courses,id',
                    'option_id' => 'nullable|array',
                    'option_id.*' => 'nullable|integer|exists:options,id',
                    'merchandise_id' => 'nullable|array',
                    'merchandise_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyle_id' => 'nullable|array',
                    'hairstyle_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'required|array',
                    'user_id.*' => 'required|integer|exists:users,id',
                ]);

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }


                // 顧客を作成
                $customer = Customer::create([
                    'customer_name' => $validatedData['customer_name'],
                    'phone_number' => $validatedData['phone_number'],
                    'remarks' => $validatedData['remarks'],
                    'owner_id' => $ownerId
                ]);


                $customersCacheKey = 'owner_' . $ownerId . 'customers';

                Cache::forget($customersCacheKey);

                // 中間テーブルにデータを挿入
                // 中間テーブルにデータを挿入
                $courseIds = $validatedData['course_id'] ?? [];
                $optionIds = $validatedData['option_id'] ?? [];
                $merchandiseIds = $validatedData['merchandise_id'] ?? [];
                $hairstyleIds = $validatedData['hairstyle_id'] ?? [];
                $userIds = $validatedData['user_id'];

                $pivotData = [];
                if (!empty($courseIds)) {
                    foreach ($courseIds as $courseId) {
                        $pivotData[$courseId] = ['owner_id' => $ownerId];
                    }
                }

                // `course_id`がnullまたは空の場合、`sync`メソッドは空の配列を渡します。
                $customer->courses()->sync($pivotData);

                $pivotData = [];
                if (!empty($optionIds)) {
                    foreach ($optionIds as $optionId) {
                        $pivotData[$optionId] = ['owner_id' => $ownerId];
                    }
                }

                $customer->options()->sync($pivotData);

                $pivotData = [];
                if (!empty($merchandiseIds)) {
                    foreach ($merchandiseIds as $merchandiseId) {
                        $pivotData[$merchandiseId] = ['owner_id' => $ownerId];
                    }
                }

                $customer->merchandises()->sync($pivotData);

                $pivotData = [];
                if (!empty($hairstyleIds)) {
                    foreach ($hairstyleIds as $hairstyleId) {
                        $pivotData[$hairstyleId] = ['owner_id' => $ownerId];
                    }
                }

                $customer->hairstyles()->sync($pivotData);


                $pivotData = [];
                foreach ($userIds as $userId) {
                    $pivotData[$userId] = ['owner_id' => $ownerId];
                }
                $customer->users()->sync($pivotData);


                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $courseCustomersCache = 'owner_' . $ownerId . 'course_customers';

                Cache::forget($courseCustomersCache);

                $courseCustomer = Cache::remember($courseCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  CourseCustomer::where('owner_id', $ownerId)->get();
                });

                $optionCustomersCache = 'owner_' . $ownerId . 'option_customers';

                Cache::forget($optionCustomersCache);

                $optionCustomer = Cache::remember($optionCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  OptionCustomer::where('owner_id', $ownerId)->get();
                });

                $merchandiseCustomersCache = 'owner_' . $ownerId . 'merchandise_customers';

                Cache::forget($merchandiseCustomersCache);

                $merchandiseCustomer = Cache::remember($merchandiseCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  MerchandiseCustomer::where('owner_id', $ownerId)->get();
                });

                $hairstyleCustomersCache = 'owner_' . $ownerId . 'hairstyle_customers';

                Cache::forget($hairstyleCustomersCache);

                $hairstyleCustomer = Cache::remember($hairstyleCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  HairstyleCustomer::where('owner_id', $ownerId)->get();
                });


                $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();

                DB::commit();

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
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
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
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {

                $validatedData = $request->validate([
                    'customer_name' => 'required|string',
                    'phone_number' => 'nullable',
                    'remarks' => 'nullable',
                    'course_id' => 'array|nullable',
                    'course_id.*' => 'nullable|integer|exists:courses,id',
                    'option_id' => 'nullable|array',
                    'option_id.*' => 'nullable|integer|exists:options,id',
                    'merchandise_id' => 'nullable|array',
                    'merchandise_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyle_id' => 'nullable|array',
                    'hairstyle_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'required|array',
                    'user_id.*' => 'required|integer|exists:users,id',
                ]);


                // 指定されたIDの顧客データを取得
                $customer = Customer::find($request->id);

                // 顧客データを更新
                $customer->customer_name = $validatedData['customer_name'];
                $customer->phone_number = $validatedData['phone_number'] ?? "";
                $customer->remarks = $validatedData['remarks'] ?? "";

                $customer->save();

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }


                $customersCacheKey = 'owner_' . $ownerId . 'customers';

                Cache::forget($customersCacheKey);


                // 中間テーブルにデータを挿入
                $courseIds = $validatedData['course_id'] ?? [];
                $optionIds = $validatedData['option_id'] ?? [];
                $merchandiseIds = $validatedData['merchandise_id'] ?? [];
                $hairstyleIds = $validatedData['hairstyle_id'] ?? [];
                $userIds = $validatedData['user_id'];

                $pivotData = [];
                if (!empty($courseIds)) {
                    foreach ($courseIds as $courseId) {
                        $pivotData[$courseId] = ['owner_id' => $ownerId];
                    }
                }

                // `course_id`がnullまたは空の場合、`sync`メソッドは空の配列を渡します。
                $customer->courses()->sync($pivotData);

                $pivotData = [];
                if (!empty($optionIds)) {
                    foreach ($optionIds as $optionId) {
                        $pivotData[$optionId] = ['owner_id' => $ownerId];
                    }
                }

                $customer->options()->sync($pivotData);

                $pivotData = [];
                if (!empty($merchandiseIds)) {
                    foreach ($merchandiseIds as $merchandiseId) {
                        $pivotData[$merchandiseId] = ['owner_id' => $ownerId];
                    }
                }

                $customer->merchandises()->sync($pivotData);

                $pivotData = [];
                if (!empty($hairstyleIds)) {
                    foreach ($hairstyleIds as $hairstyleId) {
                        $pivotData[$hairstyleId] = ['owner_id' => $ownerId];
                    }
                }

                $customer->hairstyles()->sync($pivotData);

                $pivotData = [];
                foreach ($userIds as $userId) {
                    $pivotData[$userId] = ['owner_id' => $ownerId];
                }
                $customer->users()->sync($pivotData);

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $courseCustomersCache = 'owner_' . $ownerId . 'course_customers';



                Cache::forget($courseCustomersCache);

                $courseCustomer = Cache::remember($courseCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  CourseCustomer::where('owner_id', $ownerId)->get();
                });

                $optionCustomersCache = 'owner_' . $ownerId . 'option_customers';

                Cache::forget($optionCustomersCache);

                $optionCustomer = Cache::remember($optionCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  OptionCustomer::where('owner_id', $ownerId)->get();
                });

                $merchandiseCustomersCache = 'owner_' . $ownerId . 'merchandise_customers';

                Cache::forget($merchandiseCustomersCache);

                $merchandiseCustomer = Cache::remember($merchandiseCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  MerchandiseCustomer::where('owner_id', $ownerId)->get();
                });

                $hairstyleCustomersCache = 'owner_' . $ownerId . 'hairstyle_customers';

                Cache::forget($hairstyleCustomersCache);

                $hairstyleCustomer = Cache::remember($hairstyleCustomersCache, $expirationInSeconds, function () use ($ownerId) {
                    return  HairstyleCustomer::where('owner_id', $ownerId)->get();
                });

                $userCustomer = CustomerUser::where('owner_id', $ownerId)->get();

                DB::commit();


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
            DB::rollBack();
            return response()->json([
                "message" => '顧客情報更新時にエラーが発生しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $customersCacheKey = 'owner_' . $ownerId . 'customers';

                Cache::forget($customersCacheKey);



                // 指定されたIDの顧客データを取得
                $customer = Customer::find($request->id);




                if (!$customer) {
                    DB::rollBack();
                    return response()->json([
                        'message' =>
                        '顧客が見つかりません！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
                // 顧客データを削除
                $customer->delete();


                DB::commit();


                return response()->json([
                    "deleteId"  => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => "顧客情報削除時にエラーが発生しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
