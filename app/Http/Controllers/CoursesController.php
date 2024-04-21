<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\courses;

class CoursesController extends Controller
{

    public function index()
    {
        $courses = courses::all();
        return response()->json(['courses' => $courses]);
    }


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

        return response()->json([], 204);
    }

    public function show($id)
    {
        $course = courses::find($id);
        if (!$course) {
            return response()->json(['message' =>
            'course not found'], 404);
        }

        return response()->json(['course' => $course]);
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'course_name' => 'required',
            'price' => 'required',
        ]);

        $course = courses::find($id);

        $course->course_name = $validatedData['course_name'];
        $course->price = $validatedData['price'];

        $course->save();

        return response()->json(
            [],
            204
        );
    }
    public function destroy($id)
    {
        $course = courses::find($id);
        if (!$course) {
            return response()->json(['message' =>
            'course not found'], 404);
        }

        try {
            $course->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete course ', 'error' => $e->getMessage()], 500);
        }

        return response()->json(
            [],
            204
        );
    }
}
