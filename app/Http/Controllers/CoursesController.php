<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\courses;

class CoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $courses = \App\Models\courses::query()
        ->where('course_name', 'like', '%'.$search.'%')
        ->paginate(20);
        
        return view('menus.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('menus.courses.create');
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
            'course_name' => 'required',
            'price' => 'required',
        ]);

        courses::create([
            'course_name' => $validatedData['course_name'],
            'price' => $validatedData['price'],
        
        ]);

        return redirect()->route('courses.index')->with('success', '新しいコースを追加しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = \App\Models\courses::find($id);
        return view('menus.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course = \App\Models\courses::find($id);
        return view('menus.courses.edit', compact('course'));
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
            'course_name' => 'required',
            'price' => 'required',
        ]);

        $course = \App\Models\courses::find($id);

        $course->course_name = $validatedData['course_name'];
        $course->price = $validatedData['price'];

        $course->save();

        return redirect()->route('courses.index')->with('success', 'コースの更新に成功しました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        \App\Models\courses::destroy($id);
        return redirect()->route('courses.index')->with('success', 'コースの削除に成功しました。');
    }
}
