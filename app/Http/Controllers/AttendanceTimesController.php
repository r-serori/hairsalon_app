<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\attendance_times;
use App\Models\attendances;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceTimesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $attendanceId = $request->query('attendance_id');
    
        // 出席時間データを取得
        $attendanceTimes = attendance_times::where('attendance_id', $attendanceId)->orderBy('date', 'desc')->get();
    
        // 出席情報を取得
        $attendance = attendances::find($attendanceId);

    
        // ビューにデータを渡す
        return view('jobs.attendance_times.index', compact('attendanceTimes', 'attendanceId', 'attendance'));
    }
    
    /**
     * Search for attendance times.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request, $attendanceId)
    {
        // 検索フォームから入力された年月を取得
        $searchDate = $request->input('search_date');
    
        // 年月が入力されている場合、年と月に分割
        $searchYear = null;
        $searchMonth = null;
        if ($searchDate) {
            list($searchYear, $searchMonth) = explode('-', $searchDate);
        }
    
        // 出席時間データを取得するクエリを実行
        $query = attendance_times::where('attendance_id', $attendanceId);
    
        // 年の検索条件を追加
        if ($searchYear) {
            $query->whereYear('date', $searchYear);
        }
    
        // 月の検索条件を追加
        if ($searchMonth) {
            $query->whereMonth('date', $searchMonth);
        }
    
        // 出席時間データを取得
        $attendanceTimes = $query->orderBy('date', 'desc')->get();
    
        // 検索結果を表示するビューを返す
        return view('jobs.attendance_times.search_result', compact('attendanceTimes', 'attendanceId', 'searchYear', 'searchMonth'));
    }
    
    
    



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $attendanceId = $request->query('attendance_id');


        // ビューにデータを渡す
        return view('jobs.attendance_times.create', compact('attendanceId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            'attendance_id' => $request->attendance_id,
        ]);

        // 新規作成後に index 画面にリダイレクトする
        return redirect()->route('attendance_times.index', ['attendance_id' => $request->attendance_id])->with('success', '勤怠時間を新規作成に成功しました。');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $attendanceId = $request->query('attendance_id');
    
        // 検索フォームから入力された年月を取得
        $searchDate = $request->input('search_date');
    
        // 出席時間データを取得するクエリを実行
        $query = attendance_times::where('attendance_id', $attendanceId);
    
        // 日付の検索条件を追加
        if ($searchDate) {
            $query->where('date', 'LIKE', "$searchDate%");
        }
    
        // 出席時間データを取得
        $attendanceTimes = $query->orderBy('date', 'desc')->get();
    
        // 出席情報を取得
        $attendance = attendances::find($attendanceId);
    
        // ビューにデータを渡す
        return view('jobs.attendance_times.show', compact('attendanceTimes', 'attendanceId'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $attendanceTime = attendance_times::find($id);
        // $attendance からデータを取得してビューに渡す
        // $attendanceTimes からデータを取得してビューに渡す
        return view('jobs.attendance_times.edit', compact('attendanceTime'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $attendanceTime->attendance_id = $request->attendance_id;

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
