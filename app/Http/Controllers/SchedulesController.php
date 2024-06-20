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

class SchedulesController extends Controller
{
    public function index($id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $currentYear = Carbon::now()->year;

                $selectSchedules = schedules::where('owner_id', $id)->whereRaw('DATE_FORMAT(start_time, "%Y") = ? OR DATE_FORMAT(start_time, "%Y") = ?', [$currentYear, $currentYear + 1])->get();

                $customers = customers::where('owner_id', $id)->get();

                $courses = courses::where('owner_id', $id)->get();

                $options = options::where('owner_id', $id)->get();

                $merchandises = merchandises::where('owner_id', $id)->get();

                $hairstyles = hairstyles::where('owner_id', $id)->get();

                $owner = owner::find($id);

                $staff = staff::where('owner_id', $id)->pluck('user_id');

                if ($staff->isEmpty()) {
                    $users = User::find($owner->user_id);
                } else {
                    $users = User::whereIn('id', $staff)->get();
                }

                $courseCustomer = course_customers::where('owner_id', $id)->get();

                $optionCustomer = option_customers::where('owner_id', $id)->get();

                $merchandiseCustomer = merchandise_customers::where('owner_id', $id)->get();

                $hairstyleCustomer = hairstyle_customers::where('owner_id', $id)->get();

                $userCustomer = customer_users::where('owner_id', $id)->get();

                if ($selectSchedules->isEmpty()) {
                    return response()->json([
                        "resStatus" => "success",
                        'message' =>
                        '初めまして！新規作成ボタンからスケジュールを作成しましょう！',
                        'schedules' => $selectSchedules,
                        'customers' => $customers,
                        'courses' => $courses,
                        'options' => $options,
                        'merchandises' => $merchandises,
                        'hairstyles' => $hairstyles,
                        'users' => $users->only(['id', 'name']),
                        'course_customers' => $courseCustomer,
                        'option_customers' => $optionCustomer,
                        'merchandise_customers' => $merchandiseCustomer,
                        'hairstyle_customers' => $hairstyleCustomer,
                        'customer_users' => $userCustomer,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {

                    return response()->json([
                        "resStatus" => "success",
                        'schedules' => $selectSchedules,
                        'customers' => $customers,
                        'courses' => $courses,
                        'options' => $options,
                        'merchandises' => $merchandises,
                        'hairstyles' => $hairstyles,
                        'users'
                        => $users->only(['id', 'name']),
                        'course_customers' => $courseCustomer,
                        'option_customers' => $optionCustomer,
                        'merchandise_customers' => $merchandiseCustomer,
                        'hairstyle_customers' => $hairstyleCustomer,
                        'customer_users' => $userCustomer,
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    '権限がありません。'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールが見つかりません。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function selectGetYear($id, $year)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {
                $selectGetYear = $year;

                $selectSchedules = schedules::where('owner_id', $id)->whereRaw('DATA_FORMAT(start_time, "%Y") = ?', [$selectGetYear])
                    ->get();

                $customers = customers::where('owner_id', $id)->get();

                $courses = courses::where('owner_id', $id)->get();

                $options = options::where('owner_id', $id)->get();

                $merchandises = merchandises::where('owner_id', $id)->get();

                $hairstyles = hairstyles::where('owner_id', $id)->get();


                $owner = owner::find($id);

                $staff = staff::where('owner_id', $id)->pluck('user_id');

                if ($staff->isEmpty()) {
                    $users = User::find($owner->user_id);
                } else {
                    $users = User::whereIn('id', $staff)->get();
                }

                $courseCustomer = course_customers::where('owner_id', $id)->get();

                $optionCustomer = option_customers::where('owner_id', $id)->get();

                $merchandiseCustomer = merchandise_customers::where('owner_id', $id)->get();

                $hairstyleCustomer = hairstyle_customers::where('owner_id', $id)->get();

                $userCustomer = customer_users::where('owner_id', $id)->get();

                return response()->json([
                    "resStatus" => "success",
                    'schedules' => $selectSchedules,
                    'customers' => $customers,
                    'courses' => $courses,
                    'options' => $options,
                    'merchandises' => $merchandises,
                    'hairstyles' => $hairstyles,
                    'users' => $users->only(['id', 'name']),
                    'course_customers' => $courseCustomer,
                    'option_customers' => $optionCustomer,
                    'merchandise_customers' => $merchandiseCustomer,
                    'hairstyle_customers' => $hairstyleCustomer,
                    'customer_users' => $userCustomer,
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    '権限がありません。'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールが見つかりません。'
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
                    'customers_id' => 'nullable',
                    'customers_id.*' => 'nullable|integer|exists:customers,id',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                $schedule = schedules::create([
                    'title' => $validatedData['title'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'allDay' => $validatedData['allDay'],
                    'owner_id' => $validatedData['owner_id'],
                    'customers_id' => $validatedData['customers_id'],
                ]);



                // if ($customerId !== null && $customerId !== 0) {
                //     $customerSchedule =  $schedule->customer()->sync([$customerId]);

                //     return response()->json([
                //         "resStatus" => "success",
                //         'schedule' => $schedule,
                //         'customerSchedule' => $customerSchedule

                //     ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                // } else {
                return response()->json([
                    "resStatus" => "success",
                    'schedule' => $schedule,
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    '権限がありません。'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールの作成に失敗しました。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    // public function show($id)
    // {
    //     try {
    //         if (Gate::allows(Permissions::OWNER_PERMISSION)) {
    //             $schedule = schedules::find($id);

    //             return response()->json([
    //                 "resStatus" => "success",
    //                 'schedule' => $schedule
    //             ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         } else {
    //             return response()->json([
    //                 "resStatus" => "error",
    //                 'message' =>
    //                 '権限がありません。'
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             'message' =>
    //             'スケジュールが見つかりません。'
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
                        "resStatus" => "success",
                        "schedule" => $schedule
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    '権限がありません。'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールが見つかりません。'
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
                        "resStatus" => "error",
                        'message' =>
                        'スケジュールが見つかりません。'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $schedule->delete();
                return response()->json([
                    "resStatus" => "success",
                    "deleteId" => $request->id
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    '権限がありません。'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールの削除に失敗しました。'
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
                    'users_id' => 'nullable|array',
                    'users_id.*' => 'nullable|integer|exists:users,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
                    'customers_id' => 'nullable',
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
                $userIds = $validatedData['users_id'];


                $customer->courses()->sync($courseIds);
                $customer->options()->sync($optionIds);
                $customer->merchandises()->sync($merchandiseIds);
                $customer->hairstyles()->sync($hairstyleIds);
                $customer->users()->sync($userIds);

                $courseCustomer = course_customers::where('customers_id', $customer->id)->get();

                $optionCustomer = option_customers::where('customers_id', $customer->id)->get();

                $merchandiseCustomer = merchandise_customers::where('customers_id', $customer->id)->get();

                $hairstyleCustomer = hairstyle_customers::where('customers_id', $customer->id)->get();

                $userCustomer = customer_users::where('customers_id', $customer->id)->get();


                $validatedData = $request->validate([
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
                ]);

                $schedule = schedules::create([
                    'title' => $validatedData['title'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'allDay' => $validatedData['allDay'],
                    'customers_id' => $customer->id,
                ]);


                return response()->json(
                    [
                        "resStatus" => "success",
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
                    "resStatus" => "error",
                    'message' =>
                    '権限がありません。'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールと顧客情報の作成に失敗しました。'
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
                    'users_id' => 'nullable|array',
                    'users_id.*' => 'nullable|integer|exists:users,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
                    'customers_id' => 'required',

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
                $userIds = $validatedData['users_id'];

                $customer->courses()->sync($courseIds);
                $customer->options()->sync($optionIds);
                $customer->merchandises()->sync($merchandiseIds);
                $customer->hairstyles()->sync($hairstyleIds);
                $customer->users()->sync($userIds);

                $customer->save();

                $courseCustomer = course_customers::where('customers_id', $customer->id)->get();

                $optionCustomer = option_customers::where('customers_id', $customer->id)->get();

                $merchandiseCustomer = merchandise_customers::where('customers_id', $customer->id)->get();

                $hairstyleCustomer = hairstyle_customers::where('customers_id', $customer->id)->get();

                $userCustomer = customer_users::where('customers_id', $customer->id)->get();


                $schedule = schedules::find($validatedData['Sid']);

                $schedule->title = $validatedData['title'];
                $schedule->start_time = $validatedData['start_time'];
                $schedule->end_time = $validatedData['end_time'];
                $schedule->allDay = $validatedData['allDay'];
                $schedule->customers_id = $customerId;

                $schedule->save();

                return response()->json(
                    [
                        "resStatus" => "success",
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
                    "resStatus" => "error",
                    'message' =>
                    '権限がありません。'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールと顧客情報の更新に失敗しました。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function customerOnlyUpdate(Request $request, $id)
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
                    'users_id' => 'nullable|array',
                    'users_id.*' => 'nullable|integer|exists:users,id',
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
                $userIds = $validatedData['users_id'];

                $courseCustomer = $customer->courses()->sync($courseIds);
                $optionCustomer = $customer->options()->sync($optionIds);
                $merchandiseCustomer = $customer->merchandises()->sync($merchandiseIds);
                $hairstyleCustomer = $customer->hairstyles()->sync($hairstyleIds);
                $userCustomer = $customer->users()->sync($userIds);

                $customer->save();

                $courseCustomer = course_customers::where('customers_id', $customer->id)->get();

                $optionCustomer = option_customers::where('customers_id', $customer->id)->get();

                $merchandiseCustomer = merchandise_customers::where('customers_id', $customer->id)->get();

                $hairstyleCustomer = hairstyle_customers::where('customers_id', $customer->id)->get();

                $userCustomer = customer_users::where('customers_id', $customer->id)->get();

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
                        "resStatus" => "success",
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
                    "resStatus" => "error",
                    'message' =>
                    '権限がありません。'
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールと顧客情報の更新に失敗しました。'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
