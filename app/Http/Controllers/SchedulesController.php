<?php

namespace App\Http\Controllers;

use App\Models\courses;
use Illuminate\Http\Request;
use App\Models\schedules;
use App\Models\customers;
use App\Models\options;
use App\Models\merchandises;
use App\Models\hairstyles;
use Illuminate\Pagination\LengthAwarePaginator;


class SchedulesController extends Controller
{
    public function index()
    {
        $schedules = schedules::with(['courses', 'options', 'merchandises', 'hairstyles'])
        ->orderBy('date')
        ->orderBy('start_time')
        ->get();

         // 日付ごとに予定をグループ化
    $groupedSchedules = $schedules->groupBy(function ($schedule) {
        return substr($schedule->date, 0, 10); // 年月日の部分のみを取得
    });

      // ページネーション用に変換
      $currentPage = LengthAwarePaginator::resolveCurrentPage();
      $perPage = 20;
      $currentPageItems = $groupedSchedules->slice(($currentPage - 1) * $perPage, $perPage)->all();
      $paginatedSchedules = new LengthAwarePaginator($currentPageItems, count($groupedSchedules), $perPage);

    return view('jobs.schedules.index', compact('paginatedSchedules'));

    }


    public function create()
    {
        $customers = customers::all();
        $courses = courses::all();
        $options = options::all();
        $merchandises = merchandises::all();
        $hairstyles = hairstyles::all();

        return view('jobs.schedules.create', compact('customers', 'courses', 'options', 'merchandises', 'hairstyles'));
    }




    public function fromCustomersStore(Request $request, $id)
    {


        $customer = customers::find($id);
        $customerId = $customer->id;
        

        // バリデーションルールを定義する
        $validatedData = $request->validate([
            'customer_name' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'courses_id' => 'nullable|array',
            'courses_id.*' => 'nullable|exists:courses,id',
            'options_id' => 'nullable|array',
            'options_id.*' => 'nullable|exists:options,id',
            'merchandises_id' => 'nullable|array',
            'merchandises_id.*' => 'nullable|exists:merchandises,id',
            'hairstyles_id' => 'nullable|array',
            'hairstyles_id.*' => 'nullable|exists:hairstyles,id',
        ]);


        $selectedCourses = $request->input('courses_id');
        $selectedOptions = $request->input('options_id');
        $selectedMerchandises = $request->input('merchandises_id');

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




        $schedule = schedules::create([
            'customer_name' => $validatedData['customer_name'],
            'date' => $validatedData['date'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'price' => $totalPrice,
            'customer_id' => $customerId,
        ]);
     

        // 顧客とコースの中間テーブルにデータを保存
        $schedule->courses()->sync($validatedData['courses_id']);
        $schedule->options()->sync($validatedData['options_id']);
        $schedule->merchandises()->sync($validatedData['merchandises_id']);
        $schedule->hairstyles()->sync($validatedData['hairstyles_id']);

        // リダイレクトやメッセージを返す
       return redirect()->route('schedules.index')->with('success', 'Schedule created successfully');
    }

    public function store(Request $request)
    {
        // バリデーションルールを定義する

                // バリデーションルールを定義する
                $validatedData = $request->validate([
                    'customer_name' => 'required',
                    'date' => 'required',
                    'start_time' => 'required',
                    'end_time' => 'required',
                    'courses_id' => 'nullable|array',
                    'courses_id.*' => 'nullable|exists:courses,id',
                    'options_id' => 'nullable|array',
                    'options_id.*' => 'nullable|exists:options,id',
                    'merchandises_id' => 'nullable|array',
                    'merchandises_id.*' => 'nullable|exists:merchandises,id',
                    'hairstyles_id' => 'nullable|array',
                    'hairstyles_id.*' => 'nullable|exists:hairstyles,id',
                ]);
        


    
        $selectedCourses = $request->input('courses_id');
        $selectedOptions = $request->input('options_id');
        $selectedMerchandises = $request->input('merchandises_id');

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



        $schedule = schedules::create([
            'customer_name' => $validatedData['customer_name'],
            'date' => $validatedData['date'],
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'price' => $totalPrice,
        ]);
     

        // 顧客とコースの中間テーブルにデータを保存
        $schedule->courses()->sync($validatedData['courses_id']);
        $schedule->options()->sync($validatedData['options_id']);
        $schedule->merchandises()->sync($validatedData['merchandises_id']);
        $schedule->hairstyles()->sync($validatedData['hairstyles_id']);

 



        // リダイレクトやメッセージを返す
       return redirect()->route('schedules.index')->with('success', 'Schedule created successfully');
    }


    public function show($id)
    {
    }

    public function edit($id)
    {
    $schedule = schedules::find($id);
    $courses = courses::all();
    $options = options::all();
    $merchandises = merchandises::all();
    $hairstyles = hairstyles::all();

    $course_schedules = $schedule->courses;
    $option_schedules = $schedule->options;
    $merchandise_schedules = $schedule->merchandises;
    $hairstyle_schedules = $schedule->hairstyles;

    return view('jobs.schedules.edit', compact(
        'schedule',
        'hairstyles', 
        'courses', 
        'options', 
        'merchandises', 
        'course_schedules', 
        'option_schedules', 
        'merchandise_schedules', 
        'hairstyle_schedules'
    ));



    

    }

    public function update(Request $request, $id)
    {
        
    // スケジュールを検索して取得する
    $schedule = schedules::findOrFail($id);

    // バリデーションルールを定義する
    $validatedData = $request->validate([
        'customer_name' => 'required',
        'date' => 'required',
        'start_time' => 'required',
        'end_time' => 'required',
        'courses_id' => 'nullable|array',
        'courses_id.*' => 'nullable|exists:courses,id',
        'options_id' => 'nullable|array',
        'options_id.*' => 'nullable|exists:options,id',
        'merchandises_id' => 'nullable|array',
        'merchandises_id.*' => 'nullable|exists:merchandises,id',
        'hairstyles_id' => 'nullable|array',
        'hairstyles_id.*' => 'nullable|exists:hairstyles,id',
    ]);

    // 選択されたコースの価格を取得して合計する
    $totalPrice = 0;
    foreach ($validatedData['courses_id'] as $courseId) {
        $course = courses::find($courseId);
        if ($course) {
            $totalPrice += $course->price;
        }
    }

    // 選択されたオプションの価格を取得して合計する
    foreach ($validatedData['options_id'] as $optionId) {
        $option = options::find($optionId);
        if ($option) {
            $totalPrice += $option->price;
        }
    }

    // 選択された商品の価格を取得して合計する
    foreach ($validatedData['merchandises_id'] as $merchandiseId) {
        $merchandise = merchandises::find($merchandiseId);
        if ($merchandise) {
            $totalPrice += $merchandise->price;
        }
    }

    // スケジュールの各フィールドを更新する
    $schedule->update([
        'customer_name' => $validatedData['customer_name'],
        'date' => $validatedData['date'],
        'start_time' => $validatedData['start_time'],
        'end_time' => $validatedData['end_time'],
        'price' => $totalPrice,
    ]);

    // 顧客とコースの中間テーブルにデータを保存
    $schedule->courses()->sync($validatedData['courses_id']);
    $schedule->options()->sync($validatedData['options_id']);
    $schedule->merchandises()->sync($validatedData['merchandises_id']);
    $schedule->hairstyles()->sync($validatedData['hairstyles_id']);

    // リダイレクトやメッセージを返す
    return redirect()->route('schedules.index')->with('success', 'Schedule updated successfully');
}

    

    public function destroy($id)
    {
        $schedule = schedules::find($id);
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Schedule deleted successfully');
    }
}
