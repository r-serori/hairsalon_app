<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Attendance_times;
use Illuminate\Support\Facades\Log;


class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $attendances = Attendance::all(); // または適切なクエリを使用してデータを取得する
        Log::info('Attendances data:', ['attendances' => $attendances]);
    
        return view('jobs.attendances.index', compact('attendances'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('jobs.attendances.create');
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Attendance::create($request->all());
        return redirect('attendance');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
            
            $attendance = Attendance::find($id);
            return view('jobs.attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $attendance = Attendance::find($id);
    
        // if (!$attendance) {
        //     return redirect()->route('attendance.index')->with('error', 'Attendance not found.');
        // }
    
        $attendanceTimes = Attendance_times::where('attendance_id', $attendance->id)->get();
    
        return view('jobs.attendances.edit', compact('attendance', 'attendanceTimes'));
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
        $attendance = Attendance::find($id);
    
        // $attendance 変数の値をログに出力
        Log::info('Attendance data:', ['attendance' => $attendance]);
    
        $attendance->update($request->all());
        return redirect('attendance');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Attendance::destroy($id);

            
        return redirect()->route('attendance.index')->with('success', 'Attendance time deleted successfully');
    }
    
}
