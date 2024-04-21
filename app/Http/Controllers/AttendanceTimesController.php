<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\attendance_times;
use App\Models\attendances;

class AttendanceTimesController extends Controller
{

    public function index()
    {
        $attendanceTimes = attendance_times::all();
        return response()->json(['attendanceTimes' => $attendanceTimes]);
    }


    public function store(Request $request)
    {
        // リクエストからデータを検証
        $request->validate([
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'break_time' => 'nullable',
        ]);

        // リクエストから受け取ったデータを使用してレコードを作成
        attendance_times::create([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'break_time' => $request->break_time,
        ]);

        return
            response()->json(
                [],
                204
            );
    }



    public function show($id)
    {
        $attendanceTime = attendance_times::find($id);

        if (!$attendanceTime) {
            return response()->json(['error' => '勤怠時間が見つかりませんでした。'], 404);
        }

        return response()->json(['attendanceTime' => $attendanceTime]);
    }


    public function update(Request $request, $id)
    {
        // リクエストからデータを検証
        $request->validate([
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'break_time' => 'nullable',
        ]);

        // リクエストから受け取ったデータを使用してレコードを更新
        $attendanceTime = attendance_times::find($id);

        $attendanceTime->date = $request->date;
        $attendanceTime->start_time = $request->start_time;
        $attendanceTime->end_time = $request->end_time;
        $attendanceTime->break_time = $request->break_time;
        $attendanceTime->save();


        // 更新後に index 画面にリダイレクトする
        return redirect()->route('attendance_times.index', ['attendance_id' => $request->attendance_id])->with('success', '勤怠時間の更新に成功しました。');
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        $attendanceId = attendance_times::find($id);

        // レコードを削除
        attendance_times::destroy($id);

        // 削除後に index 画面にリダイレクトする



        return redirect()->route('attendance_times.index', ['attendance_id' => $attendanceId->attendance_id])->with('success', '勤怠時間の削除に成功しました。');
    }
}
