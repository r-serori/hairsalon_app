<?php

namespace App\Http\Controllers;

use App\Models\courses;
use Illuminate\Http\Request;
use App\Models\schedules;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\customers;
use DateTime;
use Illuminate\Support\Facades\Log;


class SchedulesController extends Controller
{
    public function index()
    {
        $schedules = schedules::all();

        return response()->json(['schedules' => $schedules]);
    }

    public function store(Request $request)
    {
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

        if ($customerId !== null && $customerId !== 0) {
            $schedule->customer()->sync([$customerId]);

            Log::debug('scheduleだよ', [$schedule]);
        } else {
            return;
        }


        return response()->json([], 204);
    }

    public function show($id)
    {
        $schedule = schedules::find($id);

        return response()->json(['schedule' => $schedule]);
    }


    public function edit($id)
    {
        $schedule = schedules::find($id);
        if (!$schedule) {
            return response()->json(['message' =>
            'schedule not found'], 404);
        }

        return response()->json(['schedule' => $schedule]);
    }


    public function update(Request $request, $id)
    {
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
            [],
            204
        );
    }

    public function destroy($id)
    {
        $schedule = schedules::find($id);
        if (!$schedule) {
            return response()->json(['message' =>
            'schedule not found'], 404);
        }

        try {
            $schedule->delete();
            return response()->json([], 204);
        } catch (\Exception $e) {
            return response()->json(['message' =>
            'schedule not found'], 404);
        }
    }

    public function double(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'nullable',
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
            'title' => 'nullable',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'allDay' => 'required',
            'customers_id' => 'nullable',
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

        $customerId = $customer->id;

        $validatedData = $request->validate([
            'title' => 'nullable',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'allDay' => 'required',
        ]);

        // $startTimeString = $validatedData['start_time'];


        // Log::debug('送られてきてすぐのstartTimeString', [$startTimeString]);

        // $endTimeString = $validatedData['end_time'];

        // Log::debug('送られてきてすぐのendTimeString', [$endTimeString]);

        // $startT = new DateTime($startTimeString);

        // Log::debug('new DateTimeしたstartT', [$startT]);

        // $endT = new DateTime($endTimeString);

        // Log::debug('new DateTimeしたendT', [$endT]);

        // $startTime = $startT->format('Y-m-d H:i:s');

        // Log::debug('formatしたstartTime', [$startTime]);

        // $endTime = $endT->format('Y-m-d H:i:s');

        // Log::debug('formatしたendTime', [$endTime]);

        $schedule = schedules::create([
            'title' => $validatedData['title'],
            // 'start_time' => $startTime,
            // 'end_time' => $endTime,
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'allDay' => $validatedData['allDay'],
        ]);

        Log::debug('scheduleすけじゅーるだよ', [$schedule]);

        $schedule->customer()->sync($customerId);

        return response()->json(
            [],
            204
        );
    }
}
