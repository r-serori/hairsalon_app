<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\courses;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;

class CoursesController extends Controller
{

    public function index()
    {
        try {
            if (Gate::allows(Permissions::ALL_PERMISSION)) {
                $courses = courses::all();
                if ($courses->isEmpty()) {
                    return response()->json([
                        "resStatus" => "success",
                        "message" => "初めまして！新規作成ボタンからコースを作成しましょう！",
                        'courses' => $courses
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        "resStatus" => "success",
                        'courses' => $courses
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "コースの取得に失敗しました。"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function store(Request $request)
    {
        try {
            if (Gate::allows(Permissions::MANAGER_PERMISSION)) {
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
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "コースの作成に失敗しました。"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $course = courses::find($id);
    //         if (!$course) {
    //             return response()->json([
    //                 'resStatus' => 'error',
    //                 'message' =>
    //                 'コースが見つかりません。'
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //         return response()->json([
    //             'resStatus' => 'success',
    //             'course' => $course
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             "message" => "コースの取得に失敗しました。"
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }

    public function update(Request $request, $id)
    {
        try {
            if (Gate::allows(Permissions::MANAGER_PERMISSION)) {
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
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
    public function destroy($id)
    {
        try {
            if (Gate::allows(Permissions::OWNER_PERMISSION)) {
                $course = courses::find($id);
                if (!$course) {
                    return response()->json([
                        "resStatus" => "error",
                        'message' =>
                        'コースが見つかりません。'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $course->delete();

                return response()->json(
                    [
                        "resStatus" => "success",
                        "deleteId"  => $id
                    ],
                    200
                );
            } else {
                return response()->json([
                    "resStatus" => "error",
                    "message" => "権限がありません"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                "message" => "コースの削除に失敗しました。"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
