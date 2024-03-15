<?php

namespace App\Http\Controllers;

use App\Models\courses;
use Illuminate\Http\Request;
use App\Models\schedules;
use App\Models\customers;
use App\Models\options;
use App\Models\merchandises;

class SchedulesController extends Controller
{
    public function index()
    {
        $schedules = schedules::all();
        $customers = customers::all();

        return view('jobs.schedules.index', compact('schedules'));
    }
    public function create()
    {
        $customers = customers::all();
        $courses = courses::all();
        $options = options::all();
        $merchandises = merchandises::all();
        
        return view('jobs.schedules.create', compact('customer_prices', 'customers'));
    }
    

    public function store(Request $request)
    {
        // バリデーションルールを定義する
    
        // フォームから送信されたデータを受け取る
        $customerName = $request->input('customer_name');
        $reservationDate = $request->input('date');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $selectedCourses = $request->input('courses');
        $selectedOptions = $request->input('options');
        $selectedMerchandises = $request->input('merchandises');

    
        // 顧客を作成する（必要ならば）
    
        // 予約を作成する
        $schedule = new schedules();
        $schedule->name = $customerName;
        $schedule->date = $reservationDate;
        $schedule->start_time = $startTime;
        $schedule->end_time = $endTime;
    
        // 選択されたコースの価格を取得して合計する
        $totalPrice = 0;
        foreach ($selectedCourses as $courseId) {
            $course = courses::find($courseId);
            if ($course) {
                $totalPrice += $course->price;
            }
        }

        // 選択されたオプションの価格を取得して合計する
        foreach ($selectedOptions as $optionId) {
            $option = options::find($optionId);
            if ($option) {
                $totalPrice += $option->price;
            }
        }

        // 選択された商品の価格を取得して合計する
        foreach ($selectedMerchandises as $merchandiseId) {
            $merchandise = merchandises::find($merchandiseId);
            if ($merchandise) {
                $totalPrice += $merchandise->price;
            }
        }
    
        // 合計価格を予約に設定する
        $schedule->price = $totalPrice;
    
        // 予約を顧客に関連付ける（必要ならば）
    
        // 予約を保存する
        $schedule->save();
    
        // リダイレクトやメッセージを返す
        redirect()->route('schedules.index')->with('success', 'Schedule created successfully');
    }
    

    public function show($id)
    {
        
    }

    public function edit($id)
    {
        // edit メソッドの内容を記述
    }

    public function update(Request $request, $id)
    {
        // update メソッドの内容を記述
    }

    public function destroy($id)
    {
        // destroy メソッドの内容を記述
    }
}
