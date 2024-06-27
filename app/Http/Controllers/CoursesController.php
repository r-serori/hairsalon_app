<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Cache;

class CoursesController extends Controller
{

    public function index($id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER) || $user->hasRole(Roles::STAFF)) {

                $user_id = urldecode($id);


                $coursesCacheKey = 'owner_' . $user_id . 'courses';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $courses = Cache::remember($coursesCacheKey, $expirationInSeconds, function () use ($user_id) {
                    return  Course::where('owner_id', $user_id)->get();
                });


                if ($courses->isEmpty()) {
                    return response()->json([
                        "message" => "初めまして！新規作成ボタンからコースを作成しましょう！",
                        'courses' => $courses
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'courses' => $courses
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "コースの取得に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function store(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                $validatedData = $request->validate([
                    'course_name' => 'required|string',
                    'price' => 'required|integer',
                    'owner_id' => 'required|integer|exists:owners,id',
                ]);

                $course = Course::create([
                    'course_name' => $validatedData['course_name'],
                    'price' => $validatedData['price'],
                    'owner_id' => $validatedData['owner_id'],
                ]);

                $coursesCacheKey = 'owner_' . $request->owner_id . 'courses';

                Cache::forget($coursesCacheKey);

                return response()->json([
                    "course" => $course
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "コースの作成に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    // public function show($id)
    // {
    //     try {

    //         $course = Course::find($id);
    //         if (!$course) {
    //             return response()->json([
    //                 'message' =>
    //                 'コースが見つかりません！'
    //             ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //         return response()->json([
    //             'course' => $course
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "message" => "コースの取得に失敗しました！"
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }

    public function update(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER) || $user->hasRole(Roles::MANAGER)) {
                $validatedData = $request->validate([
                    'course_name' => 'required|string',
                    'price' => 'required|integer',
                ]);

                $course = Course::find($request->id);

                $course->course_name = $validatedData['course_name'];
                $course->price = $validatedData['price'];

                $course->save();

                $coursesCacheKey = 'owner_' . $request->owner_id . 'courses';

                Cache::forget($coursesCacheKey);


                return response()->json(
                    [
                        "course" => $course
                    ],
                    200
                );
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "コースの更新に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
    public function destroy(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::OWNER)) {
                $course = Course::find($request->id);
                if (!$course) {
                    return response()->json([
                        'message' =>
                        'コースが見つかりません！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $course->delete();

                $coursesCacheKey = 'owner_' . $request->owner_id . 'courses';

                Cache::forget($coursesCacheKey);


                return response()->json(
                    [
                        "deleteId"  => $request->id
                    ],
                    200
                );
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                "message" => "コースの削除に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
