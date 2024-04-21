<?php

namespace App\Http\Controllers;

use App\Models\courses;
use Illuminate\Http\Request;
use App\Models\schedules;
use Illuminate\Pagination\LengthAwarePaginator;


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
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'price' => 'required',
        ]);

        schedules::create([
            'date' => $validatedData['date'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'price' => $validatedData['price'],
        ]);

        return response()->json([], 204);
    }

    public function show($id)
    {
        $schedule = schedules::find($id);

        return response()->json(['schedule' => $schedule]);
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'price' => 'required',
        ]);

        $schedule = schedules::find($id);

        $schedule->date = $validatedData['date'];
        $schedule->start_time = $validatedData['start_time'];
        $schedule->end_time = $validatedData['end_time'];
        $schedule->price = $validatedData['price'];

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
}
