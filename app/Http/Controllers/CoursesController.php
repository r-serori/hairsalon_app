<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use App\Models\Staff;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoursesController extends Controller
{

    public function index()
    {
        try {
            $user = User::find(Auth::id());

            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {



                $staff = Staff::where('user_id', $user->id)->first();


                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }


                $coursesCacheKey = 'owner_' . $ownerId . 'courses';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                $courses = Cache::remember($coursesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                    return  Course::where('owner_id', $ownerId)->get();
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
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
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
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $validator = Validator::make($request->all(), [
                    'course_name' => 'required|string',
                    'price' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        "message" => "入力内容を確認してください！"
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validatedData = $validator->validate();

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $course = Course::create([
                    'course_name' => $validatedData['course_name'],
                    'price' => $validatedData['price'],
                    'owner_id' => $ownerId
                ]);

                $coursesCacheKey = 'owner_' . $ownerId . 'courses';

                Cache::forget($coursesCacheKey);

                DB::commit();

                return response()->json([
                    "course" => $course
                ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            } else {

                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => "コースの作成に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER)) {
                $validator = Validator::make($request->all(), [
                    'course_name' => 'required|string',
                    'price' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        "message" => "入力内容を確認してください！"
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validatedData = $validator->validate();

                $course = Course::find($request->id);

                $course->course_name = $validatedData['course_name'];
                $course->price = $validatedData['price'];

                $course->save();

                $staff = Staff::where('user_id', $user->id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $user->id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $coursesCacheKey = 'owner_' . $ownerId . 'courses';

                Cache::forget($coursesCacheKey);

                DB::commit();

                return response()->json(
                    [
                        "course" => $course,
                        "message" => "コースを更新しました！"
                    ],
                    200
                );
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => "コースの更新に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                $course = Course::find($request->id);
                if (!$course) {
                    return response()->json([
                        'message' =>
                        'コースが見つかりません！'
                    ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $course->delete();

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $coursesCacheKey = 'owner_' . $ownerId . 'courses';

                Cache::forget($coursesCacheKey);

                DB::commit();


                return response()->json(
                    [
                        "deleteId"  => $request->id,
                        "message" => "コースを削除しました！"
                    ],
                    200
                );
            } else {
                return response()->json([
                    "message" => "あなたには権限がありません！"
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => "コースの削除に失敗しました！
                もう一度お試しください！"
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
