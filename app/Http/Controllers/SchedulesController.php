<?php

namespace App\Http\Controllers;

use App\Models\courses;
use Illuminate\Http\Request;
use App\Models\schedules;
use App\Models\customers;
use App\Models\options;
use App\Models\merchandises;
use App\Models\hairstyles;
use App\Models\course_customers;
use App\Models\option_customers;
use App\Models\merchandise_customers;
use App\Models\hairstyle_customers;
use App\Models\customer_users;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use App\Models\User;
use App\Models\owner;
use App\Models\staff;
use Illuminate\Support\Facades\Auth;
use App\Enums\Roles;
use Illuminate\Support\Facades\Log;

class SchedulesController extends Controller
{
    public function index($id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $user_id = urldecode($id);

                $customers = customers::where('owner_id', $user_id)->get();

                if ($customers->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！顧客画面の新規作成ボタンから顧客を作成しましょう！",
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $currentYear = Carbon::now()->year;

                $selectSchedules = schedules::whereYear('start_time', $currentYear)
                    ->where('owner_id', 1)
                    ->get();

                $courses = courses::where('owner_id', $user_id)->get();

                $options = options::where('owner_id', $user_id)->get();

                $merchandises = merchandises::where('owner_id', $user_id)->get();

                $hairstyles = hairstyles::where('owner_id', $user_id)->get();


                $staff = staff::where('owner_id', $user_id)->pluck('user_id');
                Log::info('staff', $staff->toArray());

                if ($staff->isEmpty()) {
                    $owner = owner::where('user_id', $user_id)->first();
                    $users = User::find($owner->user_id);
                } else {
                    $owner = owner::where('user_id', $user_id)->first();
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

                $courseCustomer = course_customers::where('owner_id', $user_id)->get();

                $optionCustomer = option_customers::where('owner_id', $user_id)->get();

                $merchandiseCustomer = merchandise_customers::where('owner_id', $user_id)->get();

                $hairstyleCustomer = hairstyle_customers::where('owner_id', $user_id)->get();

                $userCustomer = customer_users::where('owner_id', $user_id)->get();

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
            return response()->json([
                'message' =>
                'スケジュールが見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function selectGetYear($id, $year)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $user_id = urldecode($id);

                $decodeYear = urldecode($year);

                $customers = customers::where('owner_id', $user_id)->get();

                if ($customers->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！顧客画面の新規作成ボタンから顧客を作成しましょう！",
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $selectGetYear = $decodeYear;

                $selectSchedules = schedules::whereYear('start_time', $selectGetYear)
                    ->where('owner_id', 1)
                    ->get();

                $courses = courses::where('owner_id', $user_id)->get();

                $options = options::where('owner_id', $user_id)->get();

                $merchandises = merchandises::where('owner_id', $user_id)->get();

                $hairstyles = hairstyles::where('owner_id', $user_id)->get();


                $staff = staff::where('owner_id', $user_id)->pluck('user_id');
                Log::info('staff', $staff->toArray());

                if ($staff->isEmpty()) {
                    $owner = owner::where('user_id', $user_id)->first();
                    $users = User::find($owner->user_id);
                } else {
                    $owner = owner::where('user_id', $user_id)->first();
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

                $courseCustomer = course_customers::where('owner_id', $user_id)->get();

                $optionCustomer = option_customers::where('owner_id', $user_id)->get();

                $merchandiseCustomer = merchandise_customers::where('owner_id', $user_id)->get();

                $hairstyleCustomer = hairstyle_customers::where('owner_id', $user_id)->get();

                $userCustomer = customer_users::where('owner_id', $user_id)->get();

                return response()->json([
                    'schedules' => $selectSchedules,
                    'customers' => $customers,
                    'courses' => $courses,
                    'options' => $options,
                    'merchandises' => $merchandises,
                    'hairstyles' => $hairstyles,
                    'users' => $responseUsers,
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
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                $validatedData = $request->validate([
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' =>
                    'nullable|in:0,1',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                $schedule = schedules::create([
                    'title' => $validatedData['title'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'allDay' => $validatedData['allDay'],
                    'owner_id' => $validatedData['owner_id'],
                ]);

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
    //         if (Gate::allows(Permissions::OWNER_PERMISSION)) {
    //             $schedule = schedules::find($id);

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
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                $validatedData = $request->validate([
                    'Sid' => 'required|integer|exists:schedules,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
                ]);

                $schedule = schedules::find($validatedData['Sid']);
                // $schedule = schedules::find($id);

                $schedule->title = $validatedData['title'];
                $schedule->start_time = $validatedData['start_time'];
                $schedule->end_time = $validatedData['end_time'];
                $schedule->allDay = $validatedData['allDay'];

                $schedule->save();

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
            return response()->json([
                'message' =>
                'スケジュールが見つかりません！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $schedule = schedules::find($request->id);
                if (!$schedule) {
                    return response()->json([
                        'message' =>
                        'スケジュールが見つかりません！
                        もう一度お試しください！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $schedule->delete();
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
            return response()->json([
                'message' =>
                'スケジュールの削除に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function double(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                $validatedData = $request->validate([
                    'customer_name' => 'required',
                    'phone_number' => 'nullable',
                    'remarks' => 'nullable',
                    'courses_id' => 'nullable|array',
                    'courses_id.*' => 'nullable|integer|exists:courses,id',
                    'options_id' => 'nullable|array',
                    'options_id.*' => 'nullable|integer|exists:options,id',
                    'merchandises_id' => 'nullable|array',
                    'merchandises_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyles_id' => 'nullable|array',
                    'hairstyles_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'nullable|array',
                    'user_id.*' => 'nullable|integer|exists:users,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
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


                $schedule = schedules::create([
                    'title' => $validatedData['title'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'allDay' => $validatedData['allDay'],
                    'customers_id' => $customer->id,
                    'owner_id' => $validatedData['owner_id'],
                ]);


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
            return response()->json([
                'message' =>
                'スケジュールの作成に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }



    public function doubleUpdate(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                $validatedData = $request->validate([
                    'Sid' => 'required|integer|exists:schedules,id',
                    'customer_name' => 'required',
                    'phone_number' => 'nullable',
                    'remarks' => 'nullable',
                    'courses_id' => 'nullable|array',
                    'courses_id.*' => 'nullable|integer|exists:courses,id',
                    'options_id' => 'nullable|array',
                    'options_id.*' => 'nullable|integer|exists:options,id',
                    'merchandises_id' => 'nullable|array',
                    'merchandises_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyles_id' => 'nullable|array',
                    'hairstyles_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'nullable|array',
                    'user_id.*' => 'nullable|integer|exists:users,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
                    'customers_id' => 'required',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                $customerId = $validatedData['customers_id'];

                $customer = customers::find($customerId);

                $customer->customer_name = $validatedData['customer_name'];
                $customer->phone_number = $validatedData['phone_number'];
                $customer->remarks = $validatedData['remarks'];




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

                $customer->save();

                $courseCustomer = course_customers::where('owner_id', $validatedData['owner_id'])->get();

                $optionCustomer = option_customers::where('owner_id', $validatedData['owner_id'])->get();

                $merchandiseCustomer = merchandise_customers::where('owner_id', $validatedData['owner_id'])->get();

                $hairstyleCustomer = hairstyle_customers::where('owner_id', $validatedData['owner_id'])->get();

                $userCustomer = customer_users::where('owner_id', $validatedData['owner_id'])->get();

                $schedule = schedules::find($validatedData['Sid']);

                $schedule->title = $validatedData['title'];
                $schedule->start_time = $validatedData['start_time'];
                $schedule->end_time = $validatedData['end_time'];
                $schedule->allDay = $validatedData['allDay'];
                $schedule->customers_id = $customerId;


                $schedule->save();

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
            return response()->json([
                'message' =>
                'スケジュールの更新に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function customerOnlyUpdate(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                $validatedData = $request->validate([
                    'customer_name' => 'required',
                    'phone_number' => 'nullable',
                    'remarks' => 'nullable',
                    'courses_id' => 'nullable|array',
                    'courses_id.*' => 'nullable|integer|exists:courses,id',
                    'options_id' => 'nullable|array',
                    'options_id.*' => 'nullable|integer|exists:options,id',
                    'merchandises_id' => 'nullable|array',
                    'merchandises_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyles_id' => 'nullable|array',
                    'hairstyles_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'nullable|array',
                    'user_id.*' => 'nullable|integer|exists:users,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
                    'customers_id' => 'required',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                $customerId = $validatedData['customers_id'];

                $customer = customers::find($customerId);

                $customer->customer_name = $validatedData['customer_name'];
                $customer->phone_number = $validatedData['phone_number'];
                $customer->remarks = $validatedData['remarks'];

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

                $customer->save();

                $schedule = schedules::create([
                    'title' => $validatedData['title'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'allDay' => $validatedData['allDay'],
                    'customers_id' => $customerId,
                    'owner_id' => $validatedData['owner_id'],
                ]);

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
            return response()->json([
                'message' =>
                'スケジュールの更新に失敗しました！
                もう一度お試しください！
                '
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
