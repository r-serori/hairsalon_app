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
        


        
        return redirect('attendances')->with('success','スタッフの新規作成に成功しました。');
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
        
        $validatedData = $request->validate([
            'attendance_name' => 'required|string',
            'position' => 'required|string',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $attendance = attendances::findOrFail($id);

        $attendance->attendance_name = $validatedData['attendance_name'];
        $attendance->position = $validatedData['position'];
        $attendance->phone_number = $validatedData['phone_number'];
        $attendance->address = $validatedData['address'];

        $attendance->save();

        
        return redirect('attendances')->with('success', 'スタッフの情報の更新に成功しました。');
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

            
        return redirect()->route('attendances.index')->with('success', 'スタッフの削除に成功しました。');
    }
    
}

