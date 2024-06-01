<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\courses;

class CoursesController extends Controller
{

    public function index()
    {
        try {
            $courses = courses::all();
            return response()->json([
                "resStatus" => "success",
                'courses' => $courses
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "コースの取得に失敗しました。"
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'course_name' => 'required',
                'price' => 'required',
            ]);

            $course = courses::create([
                'course_name' => $validatedData['course_name'],
                'price' => $validatedData['price'],
            ]);

            return response()->json([
                "resStatus" => "success",
                "course" => $course
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "コースの作成に失敗しました。"
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $course = courses::find($id);
            if (!$course) {
                return response()->json([
                    'resStatus' => 'error',
                    'message' =>
                    'コースが見つかりません。'
                ], 500);
            }
            return response()->json([
                'resStatus' => 'success',
                'course' => $course
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "コースの取得に失敗しました。"
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'course_name' => 'required',
                'price' => 'required',
            ]);

            $course = courses::find($id);

            $course->course_name = $validatedData['course_name'];
            $course->price = $validatedData['price'];

            $course->save();

            return response()->json(
                [
                    "resStatus" => "success",
                    "course" => $course
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $course = courses::find($id);
            if (!$course) {
                return response()->json([
                    "resStatus" => "error",
                    'message' =>
                    'コースが見つかりません。'
                ], 500);
            }

            $course->delete();

            return response()->json(
                [
                    "resStatus" => "success",
                    "deleteId"  => $id
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "コースの削除に失敗しました。"
            ], 500);
        }
    }
}
