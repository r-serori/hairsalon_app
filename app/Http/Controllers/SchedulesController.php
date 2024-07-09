<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
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
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SchedulesController extends Controller
{
    //owner_idを受け取り、スケジュールを取得
    public function index()

    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

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


                $currentYear = Carbon::now()->year;

                $schedulesCacheKey = 'owner_' . $ownerId . 'schedules';

                $selectSchedules = Cache::remember($schedulesCacheKey, $expirationInSeconds, function () use ($ownerId, $currentYear) {
                    return Schedule::whereYear('start_time', $currentYear)
                        ->where('owner_id', $ownerId)
                        ->get();
                });

                if ($customers->isEmpty() && $selectSchedules->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！顧客画面の新規作成ボタンから顧客を作成しましょう！",
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } elseif ($customers->isEmpty() && $selectSchedules->isNotEmpty()) {
                    $staffs = Staff::where('owner_id', $ownerId)->pluck('user_id');
                    // Log::info('staff', $staff->toArray());

                    if ($staffs->isEmpty()) {
                        $owner = Owner::find($ownerId);
                        $resUser = User::find($owner->user_id);
                        $responseUsers = ['id' => $resUser->id, 'name' => $resUser->name];
                    } else {
                        $owner = Owner::find($ownerId);
                        // Log::info('owner', $owner->toArray());
                        $OwnersUser = User::find($owner->user_id);
                        // Log::info('user', $user->toArray());
                        $staffs->push($OwnersUser->id);
                        // Log::info('staff', $staff->toArray());
                        $users = User::whereIn('id', $staffs)->get();
                        // Log::info('users', $users->toArray());
                        $responseUsers = $users->map(function ($user) {
                            return ['id' => $user->id, 'name' => $user->name];
                        });
                    }
                    return response()->json([
                        "message" => "顧客画面の新規作成ボタンから顧客を作成しましょう！",
                        'schedules' => $selectSchedules,
                        'responseUsers' => $responseUsers,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }


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
                    $resUser = User::find($owner->user_id);
                    $responseUsers = ['id' => $resUser->id, 'name' => $resUser->name];
                } else {
                    $owner = Owner::find($ownerId);
                    // Log::info('owner', $owner->toArray());
                    $OwnersUser = User::find($owner->user_id);
                    // Log::info('user', $user->toArray());
                    $staffs->push($OwnersUser->id);
                    // Log::info('staff', $staff->toArray());
                    $users = User::whereIn('id', $staffs)->get();
                    // Log::info('users', $users->toArray());
                    $responseUsers = $users->map(function ($user) {
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

                if ($selectSchedules->isEmpty()) {
                    return response()->json([
                        'message' =>
                        '初めまして！新規作成ボタンからスケジュールを作成しましょう！',
                        'schedules' => $selectSchedules,
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
                        'schedules' => $selectSchedules,
                        'customers' => $customers,
                        'courses' => $courses,
                        'options' => $options,
                        'merchandises' => $merchandises,
                        'hairstyles' => $hairstyles,
                        'responseUsers'
                        => $responseUsers,
                        'course_customers' => $courseCustomer,
                        'option_customers' => $optionCustomer,
                        'merchandise_customers' => $merchandiseCustomer,
                        'hairstyle_customers' => $hairstyleCustomer,
                        'customer_users' => $userCustomer,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'message' =>
                    'あなたには権限がありません！'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' =>
                'スケジュールが見つかりません!もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function selectGetYear($year)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $decodeYear = urldecode($year);

                $customersCacheKey = 'owner_' . $ownerId . 'customers';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $customers = Cache::remember($customersCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return  Customer::where('owner_id', $ownerId)->get();
                });

                $selectGetYear = $decodeYear;


                $schedulesCacheKey = 'owner_' . $ownerId . 'schedules';

                $selectSchedules = Cache::remember($schedulesCacheKey, $expirationInSeconds, function () use ($ownerId, $selectGetYear) {
                    return Schedule::whereYear('start_time', $selectGetYear)
                        ->where('owner_id', $ownerId)
                        ->get();
                });


                if ($customers->isEmpty() && $selectSchedules->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！顧客画面の新規作成ボタンから顧客を作成しましょう！",
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } elseif ($customers->isEmpty() && $selectSchedules->isNotEmpty()) {
                    $staffs = Staff::where('owner_id', $ownerId)->pluck('user_id');
                    // Log::info('staff', $staff->toArray());

                    if ($staffs->isEmpty()) {
                        $owner = Owner::find($ownerId);
                        $resUser = User::find($owner->user_id);
                        $responseUsers = ['id' => $resUser->id, 'name' => $resUser->name];
                    } else {
                        $owner = Owner::find($ownerId);
                        // Log::info('owner', $owner->toArray());
                        $OwnersUser = User::find($owner->user_id);
                        // Log::info('user', $user->toArray());
                        $staffs->push($OwnersUser->id);
                        // Log::info('staff', $staff->toArray());
                        $users = User::whereIn('id', $staffs)->get();
                        // Log::info('users', $users->toArray());
                        $responseUsers = $users->map(function ($user) {
                            return ['id' => $user->id, 'name' => $user->name];
                        });
                    }
                    return response()->json([
                        "message" => "顧客画面の新規作成ボタンから顧客を作成しましょう！",
                        'schedules' => $selectSchedules,
                        'responseUsers' => $responseUsers,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }


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

                $staff = Staff::where('owner_id', $ownerId)->pluck('user_id');
                Log::info('staff', $staff->toArray());

                if ($staff->isEmpty()) {
                    $owner = Owner::where('user_id', $ownerId)->first();
                    $users = User::find($owner->user_id);
                } else {
                    $owner = Owner::where('user_id', $ownerId)->first();
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

                return response()->json([
                    'schedules' => $selectSchedules,
                    'customers' => $customers,
                    'courses' => $courses,
                    'options' => $options,
                    'merchandises' => $merchandises,
                    'hairstyles' => $hairstyles,
                    'responseUsers'
                    => $responseUsers,
                    'course_customers' => $courseCustomer,
                    'option_customers' => $optionCustomer,
                    'merchandise_customers' => $merchandiseCustomer,
                    'hairstyle_customers' => $hairstyleCustomer,
                    'customer_users' => $userCustomer,
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    'message' =>
                    'あなたには権限がありません！'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' =>
                'スケジュールが見つかりません！
                もう一度お試しください！'
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
                    'title' => 'required|string',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'in:0,1',
                ]);

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $schedule = Schedule::create([
                    'title' => $validatedData['title'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'allDay' => $validatedData['allDay'],
                    'owner_id' => $ownerId,
                ]);

                $schedulesCacheKey = 'owner_' . $ownerId . 'schedules';

                Cache::forget($schedulesCacheKey);


                DB::commit();

                return response()->json([
                    'schedule' => $schedule,
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    'message' =>
                    'あなたには権限がありません！'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' =>
                'スケジュールの作成に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    // public function show($id)
    // {
    //     try {
    //         if (Gate::allows(Permissions::$OWNER_PERMISSION)) {
    //             $schedule = Schedule::find($id);

    //             return response()->json([
    //                 'schedule' => $schedule
    //             ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         } else {
    //             return response()->json([
    //                 'message' =>
    //                 'あなたには権限がありません！'
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' =>
    //             'スケジュールが見つかりません！'
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
                    'Sid' => 'required|integer|exists:schedules,id',
                    'title' => 'required|string',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
                ]);

                $schedule = Schedule::find($validatedData['Sid']);
                // $schedule = Schedule::find($id);

                $schedule->title = $validatedData['title'];
                $schedule->start_time = $validatedData['start_time'];
                $schedule->end_time = $validatedData['end_time'];
                $schedule->allDay = $validatedData['allDay'];

                $schedule->save();

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $schedulesCacheKey = 'owner_' . $ownerId . 'schedules';

                Cache::forget($schedulesCacheKey);

                DB::commit();

                return response()->json(
                    [
                        "schedule" => $schedule
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    'message' =>
                    'あなたには権限がありません！'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' =>
                'スケジュールが見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $schedule = Schedule::find($request->id);
                if (!$schedule) {
                    return response()->json([
                        'message' =>
                        'スケジュールが見つかりません！
                        もう一度お試しください！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $schedule->delete();

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $schedulesCacheKey = 'owner_' . $ownerId . 'schedules';

                Cache::forget($schedulesCacheKey);

                DB::commit();

                return response()->json([
                    "deleteId" => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    'message' =>
                    'あなたには権限がありません！'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' =>
                'スケジュールの削除に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function double(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $validatedData = $request->validate([
                    'customer_name' => 'required|string',
                    'phone_number' => 'nullable',
                    'remarks' => 'nullable',
                    'course_id' => 'nullable|array',
                    'course_id.*' => 'nullable|integer|exists:courses,id',
                    'option_id' => 'nullable|array',
                    'option_id.*' => 'nullable|integer|exists:options,id',
                    'merchandise_id' => 'nullable|array',
                    'merchandise_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyle_id' => 'nullable|array',
                    'hairstyle_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'required|array',
                    'user_id.*' => 'required|integer|exists:users,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required|in:0,1',

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
                    'owner_id' => $ownerId,
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


                $schedule = Schedule::create([
                    'title' => $validatedData['title'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'allDay' => $validatedData['allDay'],
                    'customer_id' => $customer->id,
                    'owner_id' => $ownerId,
                ]);

                $schedulesCacheKey = 'owner_' . $ownerId . 'schedules';

                Cache::forget($schedulesCacheKey);

                DB::commit();

                return response()->json(
                    [
                        "customer" => $customer,
                        "schedule" => $schedule,
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
                    'message' =>
                    'あなたには権限がありません！'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                'message' =>
                'スケジュールの作成に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }



    public function doubleUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $validatedData = $request->validate([
                    'Sid' => 'required|integer|exists:schedules,id',
                    'customer_name' => 'required|string',
                    'phone_number' => 'nullable',
                    'remarks' => 'nullable',
                    'course_id' => 'nullable|array',
                    'course_id.*' => 'nullable|integer|exists:courses,id',
                    'option_id' => 'nullable|array',
                    'option_id.*' => 'nullable|integer|exists:options,id',
                    'merchandise_id' => 'nullable|array',
                    'merchandise_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyle_id' => 'nullable|array',
                    'hairstyle_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'required|array',
                    'user_id.*' => 'required|integer|exists:users,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required|in:0,1',
                    'customer_id' => 'required',
                ]);


                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }


                $customerId = $validatedData['customer_id'];

                $customer = Customer::find($customerId);

                $customer->customer_name = $validatedData['customer_name'];
                $customer->phone_number = $validatedData['phone_number'];
                $customer->remarks = $validatedData['remarks'];

                $customer->save();

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

                $schedule = Schedule::find($validatedData['Sid']);

                $schedule->title = $validatedData['title'];
                $schedule->start_time = $validatedData['start_time'];
                $schedule->end_time = $validatedData['end_time'];
                $schedule->allDay = $validatedData['allDay'];
                $schedule->customer_id = $customerId;


                $schedule->save();

                $schedulesCacheKey = 'owner_' . $ownerId . 'schedules';

                Cache::forget($schedulesCacheKey);

                DB::commit();


                return response()->json(
                    [
                        "customer" => $customer,
                        "schedule" => $schedule,
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
                    'message' =>
                    'あなたには権限がありません！'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' =>
                'スケジュールの更新に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function customerOnlyUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $validatedData = $request->validate([
                    'customer_name' => 'required|string',
                    'phone_number' => 'nullable',
                    'remarks' => 'nullable',
                    'course_id' => 'nullable|array',
                    'course_id.*' => 'nullable|integer|exists:courses,id',
                    'option_id' => 'nullable|array',
                    'option_id.*' => 'nullable|integer|exists:options,id',
                    'merchandise_id' => 'nullable|array',
                    'merchandise_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyle_id' => 'nullable|array',
                    'hairstyle_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'nullable|array',
                    'user_id.*' => 'required|integer|exists:users,id',
                    'title' => 'required',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
                    'customer_id' => 'required',
                ]);


                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $customerId = $validatedData['customer_id'];

                $customer = Customer::find($customerId);

                $customer->customer_name = $validatedData['customer_name'];
                $customer->phone_number = $validatedData['phone_number'];
                $customer->remarks = $validatedData['remarks'];


                $customer->save();

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


                $schedule = Schedule::create([
                    'title' => $validatedData['title'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'allDay' => $validatedData['allDay'],
                    'customer_id' => $customerId,
                    'owner_id' => $ownerId,
                ]);


                $schedulesCacheKey = 'owner_' . $ownerId . 'schedules';

                Cache::forget($schedulesCacheKey);

                DB::commit();


                return response()->json(
                    [
                        "customer" => $customer,
                        "schedule" => $schedule,
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
                    'message' =>
                    'あなたには権限がありません！'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' =>
                'スケジュールの更新に失敗しました！
                もう一度お試しください！
                '
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
