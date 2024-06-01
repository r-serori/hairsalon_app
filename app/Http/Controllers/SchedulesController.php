<?php

namespace App\Http\Controllers;

use App\Models\courses;
use Illuminate\Http\Request;
use App\Models\schedules;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\customers;
use App\Models\options;
use App\Models\merchandises;
use App\Models\hairstyles;
use App\Models\attendances;
use App\Models\course_customers;
use App\Models\option_customers;
use App\Models\merchandise_customers;
use App\Models\hairstyle_customers;
use App\Models\customer_attendances;
use App\Models\customer_schedules;
use Carbon\Carbon;

class SchedulesController extends Controller
{
    public function index()
    {
        try {


            $currentYear = Carbon::now()->year;

            $selectSchedules = schedules::where('start_time', 'like', $currentYear . '%')->orWhere('start_time', 'like', $currentYear + 1 . '%')->get();

            $customers = customers::all();

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

            $customerSchedule = customer_schedules::all();


            return response()->json([
                "resStatus" => "success",
                'schedules' => $selectSchedules,
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
                'customer_schedules' => $customerSchedule,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールが見つかりません。'
            ], 500);
        }
    }
    public function selectGetYear(Request $request)
    {
        try {

            $selectSchedules = schedules::where('start_time', 'like', $request->year . '%')->get();

            $customerSchedule = $selectSchedules->map(function ($schedule) {
                return customer_schedules::where('schedules_id', $schedule->id)->get();
            });

            $customers = customers::all();

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



            return response()->json([
                "resStatus" => "success",
                'schedules' => $selectSchedules,
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
                'customer_schedules' => $customerSchedule,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールが見つかりません。'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'nullable',
                'start_time' => 'nullable',
                'end_time' => 'nullable',
                'allDay' =>
                'nullable|in:0,1',
                'customers_id' => 'nullable',
                'customers_id.*' => 'nullable|integer|exists:customers,id',
            ]);

            $schedule = schedules::create([
                'title' => $validatedData['title'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'allDay' => $validatedData['allDay'],
            ]);

            $customerId = $validatedData['customers_id'];

            // if ($customerId !== null && $customerId !== 0) {
            //     $customerSchedule =  $schedule->customer()->sync([$customerId]);

            //     return response()->json([
            //         "resStatus" => "success",
            //         'schedule' => $schedule,
            //         'customerSchedule' => $customerSchedule

            //     ], 200);
            // } else {
            return response()->json([
                "resStatus" => "success",
                'schedule' => $schedule,
            ], 200);
            // }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールの作成に失敗しました。'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $schedule = schedules::find($id);

            return response()->json([
                "resStatus" => "success",
                'schedule' => $schedule
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールが見つかりません。'
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
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
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールが見つかりません。'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = schedules::find($id);
            if (!$schedule) {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    'スケジュールが見つかりません。'
                ], 500);
            }

            $schedule->delete();
            return response()->json([
                "resStatus" => "success",
                "deleteId" => $id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールの削除に失敗しました。'
            ], 500);
        }
    }

    public function double(Request $request)
    {
        try {
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
                'attendances_id' => 'nullable|array',
                'attendances_id.*' => 'nullable|integer|exists:attendances,id',
                'title' => 'nullable',
                'start_time' => 'nullable',
                'end_time' => 'nullable',
                'allDay' => 'required',
                'customers_id' => 'nullable',
            ]);

            $customerId = $validatedData['customers_id'];

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


            $courseCustomer = $customer->courses()->sync($courseIds);
            $optionCustomer = $customer->options()->sync($optionIds);
            $merchandiseCustomer = $customer->merchandises()->sync($merchandiseIds);
            $hairstyleCustomer = $customer->hairstyles()->sync($hairstyleIds);
            $attendanceCustomer = $customer->attendances()->sync($attendanceIds);


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
            ]);

            $customerSchedule = $schedule->customer()->sync($customerId);

            return response()->json(
                [
                    "resStatus" => "success",
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールと顧客情報の作成に失敗しました。'
            ], 500);
        }
    }



    public function doubleUpdate(Request $request, $id)
    {
        try {
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
                'attendances_id' => 'nullable|array',
                'attendances_id.*' => 'nullable|integer|exists:attendances,id',
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
            $attendanceIds = $validatedData['attendances_id'];


            $courseCustomer = $customer->courses()->sync($courseIds);
            $optionCustomer = $customer->options()->sync($optionIds);
            $merchandiseCustomer = $customer->merchandises()->sync($merchandiseIds);
            $hairstyleCustomer = $customer->hairstyles()->sync($hairstyleIds);
            $attendanceCustomer = $customer->attendances()->sync($attendanceIds);

            $customer->save();

            $schedule = schedules::find($validatedData['Sid']);

            $schedule->title = $validatedData['title'];
            $schedule->start_time = $validatedData['start_time'];
            $schedule->end_time = $validatedData['end_time'];
            $schedule->allDay = $validatedData['allDay'];

            $customerSchedule = $schedule->customer()->sync($customerId);

            $schedule->save();
            return response()->json(
                [
                    "resStatus" => "success",
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールと顧客情報の更新に失敗しました。'
            ], 500);
        }
    }


    public function customerOnlyUpdate(Request $request, $id)
    {
        try {
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
                'attendances_id' => 'nullable|array',
                'attendances_id.*' => 'nullable|integer|exists:attendances,id',
                'title' => 'nullable',
                'start_time' => 'nullable',
                'end_time' => 'nullable',
                'allDay' => 'required',
                'customers_id' => 'required'
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
            $attendanceIds = $validatedData['attendances_id'];


            $courseCustomer = $customer->courses()->sync($courseIds);
            $optionCustomer = $customer->options()->sync($optionIds);
            $merchandiseCustomer = $customer->merchandises()->sync($merchandiseIds);
            $hairstyleCustomer = $customer->hairstyles()->sync($hairstyleIds);
            $attendanceCustomer = $customer->attendances()->sync($attendanceIds);

            $customer->save();

            $schedule = schedules::create([
                'title' => $validatedData['title'],
                'start_time' => $validatedData['start_time'],
                'end_time' => $validatedData['end_time'],
                'allDay' => $validatedData['allDay'],
            ]);

            $customerSchedule = $schedule->customer()->sync($customerId);


            return response()->json(
                [
                    "resStatus" => "success",
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' =>
                'スケジュールと顧客情報の更新に失敗しました。'
            ], 500);
        }
    }
}
