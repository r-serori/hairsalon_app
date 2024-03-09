<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance_times;
use App\Models\Attendance;


class AttendanceTimesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {       
        // 検索フォームで入力された日付を取得
        $search = $request->input('search');
    
        // データを取得
        $query = Attendance_times::where('attendance_id', $id);
    
        // 日付の範囲を指定して検索
        if ($search) {
            // 日付がYYYY/MMの形式で入力されていることを想定して、年月を取得して検索条件に追加
            $dateParts = explode('/', $search);
            $year = $dateParts[0];
            $month = $dateParts[1];
    
            $query->whereYear('date', $year)
                ->whereMonth('date', $month);
        }
    
        // データを取得
        $attendanceTimes = $query->get();

        $attendance = Attendance::find($id);
    
        // ビューにデータを渡す
        return view('jobs.attendance_times.show', compact('attendanceTimes', 'attendance'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        // $attendance_idに対応するデータを取得するクエリを実行
       
        
        $attendance = Attendance::find($id);

        
        // 取得したデータをビューに渡す
        return view('jobs.attendance_times.create', compact('attendance'));



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
                    'break_time' => 'required',
                ]);
            
                // リクエストから受け取ったデータを使用してレコードを作成
                Attendance_times::create([
                    'date' => $request->date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'break_time' => $request->break_time,
                    'attendance_id' => $request->attendance_id, 

                ]);
            
            
                return redirect()->route('attendance_times.index', $request->attendance_id)->with('success', 'Attendance time created successfully');
            
            

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
         // 検索フォームで入力された日付を取得
         $search = $request->input('search');
    
         // データを取得
         $query = Attendance_times::where('attendance_id', $id);
     
         // 日付の範囲を指定して検索
         if ($search) {
             // 日付がYYYY/MMの形式で入力されていることを想定して、年月を取得して検索条件に追加
             $dateParts = explode('/', $search);
             $year = $dateParts[0];
             $month = $dateParts[1];
     
             $query->whereYear('date', $year)
                 ->whereMonth('date', $month);
         }
     
         // データを取得
         $attendanceTimes = $query->get();
 
         $attendance = Attendance::find($id);
     
         // ビューにデータを渡す
         return view('jobs.attendance_times.show', compact('attendanceTimes', 'attendance'));
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $attendanceTime = Attendance_times::find($id);
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
    public function update(Request $request, $id)
    {
            

            
                // リクエストから受け取ったデータを使用してレコードを更新
                Attendance_times::where('id', $id)->update([
                    'date' => $request->date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'break_time' => $request->break_time,
                ]);
            
                return redirect()->route('attendance_times.show', $request->attendance_time)->with('success', 'Attendance time updated successfully');
            
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
            
                // レコードを削除
                Attendance_times::destroy($id);

            
                return redirect()->route('attendance.index')->with('success', 'Attendance time deleted successfully');
    }

}
