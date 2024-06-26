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
use Illuminate\Support\Facades\Log;

class SchedulesController extends Controller
{
    //owner_idを受け取り、スケジュールを取得
    public function index($owner_id)

    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $decodedOwnerId = urldecode($owner_id);

                $customers = Customer::where('owner_id', $decodedOwnerId)->get();

                if ($customers->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！顧客画面の新規作成ボタンから顧客を作成しましょう！",
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $currentYear = Carbon::now()->year;

                $selectSchedules = Schedule::whereYear('start_time', $currentYear)
                    ->where('owner_id', 1)
                    ->get();

                $courses = Course::where('owner_id', $decodedOwnerId)->get();

                $options = Option::where('owner_id', $decodedOwnerId)->get();

                $merchandises = Merchandise::where('owner_id', $decodedOwnerId)->get();

                $hairstyles = Hairstyle::where('owner_id', $decodedOwnerId)->get();


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

                $customers = Customer::where('owner_id', $user_id)->get();

                if ($customers->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！顧客画面の新規作成ボタンから顧客を作成しましょう！",
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $selectGetYear = $decodeYear;

                $selectSchedules = Schedule::whereYear('start_time', $selectGetYear)
                    ->where('owner_id', 1)
                    ->get();

                $courses = Course::where('owner_id', $user_id)->get();

                $options = Option::where('owner_id', $user_id)->get();

                $merchandises = Merchandise::where('owner_id', $user_id)->get();

                $hairstyles = Hairstyle::where('owner_id', $user_id)->get();


                $staff = Staff::where('owner_id', $user_id)->pluck('user_id');
                Log::info('staff', $staff->toArray());

                if ($staff->isEmpty()) {
                    $owner = Owner::where('user_id', $user_id)->first();
                    $users = User::find($owner->user_id);
                } else {
                    $owner = Owner::where('user_id', $user_id)->first();
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

                $courseCustomer = CourseCustomer::where('owner_id', $user_id)->get();

                $optionCustomer = OptionCustomer::where('owner_id', $user_id)->get();

                $merchandiseCustomer = MerchandiseCustomer::where('owner_id', $user_id)->get();

                $hairstyleCustomer = HairstyleCustomer::where('owner_id', $user_id)->get();

                $userCustomer = CustomerUser::where('owner_id', $user_id)->get();

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

                $schedule = Schedule::create([
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

                $schedule = Schedule::find($validatedData['Sid']);
                // $schedule = Schedule::find($id);

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
                $schedule = Schedule::find($request->id);
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
                    'course_id' => 'nullable|array',
                    'course_id.*' => 'nullable|integer|exists:courses,id',
                    'option_id' => 'nullable|array',
                    'option_id.*' => 'nullable|integer|exists:options,id',
                    'merchandise_id' => 'nullable|array',
                    'merchandise_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyle_id' => 'nullable|array',
                    'hairstyle_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'nullable|array',
                    'user_id.*' => 'nullable|integer|exists:users,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
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


                $schedule = Schedule::create([
                    'title' => $validatedData['title'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'allDay' => $validatedData['allDay'],
                    'customer_id' => $customer->id,
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
                    'course_id' => 'nullable|array',
                    'course_id.*' => 'nullable|integer|exists:courses,id',
                    'option_id' => 'nullable|array',
                    'option_id.*' => 'nullable|integer|exists:options,id',
                    'merchandise_id' => 'nullable|array',
                    'merchandise_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyle_id' => 'nullable|array',
                    'hairstyle_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'nullable|array',
                    'user_id.*' => 'nullable|integer|exists:users,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
                    'customer_id' => 'required',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                $customerId = $validatedData['customer_id'];

                $customer = Customer::find($customerId);

                $customer->customer_name = $validatedData['customer_name'];
                $customer->phone_number = $validatedData['phone_number'];
                $customer->remarks = $validatedData['remarks'];




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

                $customer->save();

                $courseCustomer = CourseCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $optionCustomer = OptionCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $merchandiseCustomer = MerchandiseCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $hairstyleCustomer = HairstyleCustomer::where('owner_id', $validatedData['owner_id'])->get();

                $userCustomer = CustomerUser::where('owner_id', $validatedData['owner_id'])->get();

                $schedule = Schedule::find($validatedData['Sid']);

                $schedule->title = $validatedData['title'];
                $schedule->start_time = $validatedData['start_time'];
                $schedule->end_time = $validatedData['end_time'];
                $schedule->allDay = $validatedData['allDay'];
                $schedule->customer_id = $customerId;


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
                    'course_id' => 'nullable|array',
                    'course_id.*' => 'nullable|integer|exists:courses,id',
                    'option_id' => 'nullable|array',
                    'option_id.*' => 'nullable|integer|exists:options,id',
                    'merchandise_id' => 'nullable|array',
                    'merchandise_id.*' => 'nullable|integer|exists:merchandises,id',
                    'hairstyle_id' => 'nullable|array',
                    'hairstyle_id.*' => 'nullable|integer|exists:hairstyles,id',
                    'user_id' => 'nullable|array',
                    'user_id.*' => 'nullable|integer|exists:users,id',
                    'title' => 'nullable',
                    'start_time' => 'nullable',
                    'end_time' => 'nullable',
                    'allDay' => 'required',
                    'customer_id' => 'required',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                $customerId = $validatedData['customer_id'];

                $customer = Customer::find($customerId);

                $customer->customer_name = $validatedData['customer_name'];
                $customer->phone_number = $validatedData['phone_number'];
                $customer->remarks = $validatedData['remarks'];

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

                $customer->save();

                $schedule = Schedule::create([
                    'title' => $validatedData['title'],
                    'start_time' => $validatedData['start_time'],
                    'end_time' => $validatedData['end_time'],
                    'allDay' => $validatedData['allDay'],
                    'customer_id' => $customerId,
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
