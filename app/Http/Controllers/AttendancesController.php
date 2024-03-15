<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Models\attendances;
use App\Models\attendance_times;
use Illuminate\Support\Facades\Log;


class AttendancesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $attendances = attendances::all(); // または適切なクエリを使用してデータを取得する
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
        
        attendances::create($request->all());
        return redirect('attendances');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
            
            $attendance = attendances::find($id);
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
        $attendance = attendances::find($id);
    
        // if (!$attendance) {
        //     return redirect()->route('attendance.index')->with('error', 'Attendance not found.');
        // }
    
    
    
        return view('jobs.attendances.edit', compact('attendance'));
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
        $attendance = attendances::find($id);
    
        // $attendance 変数の値をログに出力
        Log::info('attendances data:', ['attendances' => $attendance]);
    
        $attendance->update($request->all());
        return redirect('attendances')->with('success', 'Attendance updated successfully');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        attendances::destroy($id);

            
        return redirect()->route('attendances.index')->with('success', 'Attendance time deleted successfully');
    }
    
}

