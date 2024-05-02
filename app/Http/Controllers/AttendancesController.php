<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Models\attendances;
use Illuminate\Support\Facades\Log;


class AttendancesController extends Controller
{

    public function index()
    {

        $attendances = attendances::all(); // または適切なクエリを使用してデータを取得する
        Log::info('Attendances data:', ['attendances' => $attendances]);

        return response()->json(['attendances' => $attendances]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'attendance_name' => 'required|string',
            'position' => 'required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        attendances::create([
            'attendance_name' => $validatedData['attendance_name'],
            'position' => $validatedData['position'],
            'phone_number' => $validatedData['phone_number'],
            'address' => $validatedData['address'],
        ]);

        return
            response()->json(
                [],
                204
            );
    }

    public function show($id)
    {
        $attendance = attendances::find($id);

        if (!$attendance) {
            return response()->json(['error' => 'スタッフが見つかりませんでした。'], 404);
        }

        return response()->json(['attendance' => $attendance]);
    }

    public function edit($id)
    {
        $attendance = attendances::find($id);

        if (!$attendance) {
            return response()->json(['error' => 'スタッフが見つかりませんでした。'], 404);
        }

        return response()->json(['attendance' => $attendance]);
    }


    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'attendance_name' => 'required|string',
            'position' => 'required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $attendance = attendances::find($id);

        $attendance->attendance_name = $validatedData['attendance_name'];
        $attendance->position = $validatedData['position'];
        $attendance->phone_number = $validatedData['phone_number'];
        $attendance->address = $validatedData['address'];

        $attendance->save();

        return response()->json(
            [],
            204
        );
    }


    public function destroy($id)
    {
        $attendance = attendances::find($id);
        if (!$attendance) {
            return response()->json(['message' => 'attendance not found'], 404);
        }

        try {
            $attendance->delete();
            return response()->json(
                [],
                204
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete attendance', 'error' => $e->getMessage()], 500);
        }
    }
}
