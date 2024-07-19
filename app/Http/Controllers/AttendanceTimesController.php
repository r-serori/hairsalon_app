<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AttendanceTimesController extends Controller
{

    //クエリのuser_idを受け取り、そのユーザーの勤怠時間を１か月分取得　Gate,ALL
    //yearMonthが"000111"の場合は当月の勤怠時間を取得
    public function selectedAttendanceTime($yearMonth, $id)
    {
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {

                $user_id = urldecode($id);
                Log::info("user_id", ["user_id", $user_id]);
                $yearMonth = urldecode($yearMonth);
                Log::info("yearMonth", ["yearMonth", $yearMonth]);
                if ($yearMonth !== "000111") {
                    //わたってきた('Y-m')形式の年月を取得
                    $currentYearAndMonth = $yearMonth;
                } else {
                    // 現在の年月を取得 format('Y-m')で年月の形式に変換
                    $currentYearAndMonth = Carbon::now()->format('Y-m');
                }

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                if (empty($ownerId)) {
                    return response()->json([
                        'message' => 'オーナーが見つかりませんでした！',
                    ], 404, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $attendanceTimesCacheKey = 'owner_' . $ownerId . 'staff_' . $user_id . 'attendanceTimes';

                $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

                //user_isdでデータを絞ってから、created_atで年月を絞る
                $selectAttendanceTimes = Cache::remember($attendanceTimesCacheKey, $expirationInSeconds, function () use ($user_id, $currentYearAndMonth) {
                    return AttendanceTime::where('user_id', $user_id)
                        ->where('created_at', 'like', $currentYearAndMonth . '%')
                        ->oldest('created_at')
                        ->get();
                });

                //送信されるphoto_pathを確認
                Log::info("selectAttendanceTimes", ["selectAttendanceTimes", $selectAttendanceTimes]);

                // 各レコードのstart_photo_pathとend_photos_pathをエンコード
                $selectAttendanceTimes->transform(function ($item) {
                    $item->start_photo_path = urlencode($item->start_photo_path);
                    $item->end_photo_path = urlencode($item->end_photo_path);
                    return $item;
                });

                $user = User::find($user_id)->only([
                    'id',
                    'name',
                    'isAttendance',
                ]);

                if ($selectAttendanceTimes->isEmpty() && $yearMonth === "000111") {
                    return response()->json([
                        "message" => "初めまして！勤怠画面から出勤してください！",
                        'attendanceTimes' => $selectAttendanceTimes
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else if ($selectAttendanceTimes->isEmpty() && $yearMonth !== "000111") {
                    return response()->json([
                        "message" => "選択した勤怠履歴がありません！",
                        'attendanceTimes' => $selectAttendanceTimes
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                } else {
                    return response()->json([
                        'attendanceTimes' => $selectAttendanceTimes,
                        'responseUser' => $user
                    ], 200, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => '勤怠時間の取得に失敗しました！もう一度お試しください！'

            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function startTimeShot(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {

                // リクエストからデータを検証
                $validator = Validator::make($request->all(), [
                    'start_time' => 'required|date_format:Y-m-d H:i:s',
                    'start_photo_path' => 'required',
                    'user_id' => 'required|exists:users,id',
                ]);

                if ($validator->fails()) {
                    Log::info("validator->fails()", ["validator->fails()", $validator->fails()]);
                    return response()->json([
                        'message' => '出勤時間登録に失敗しました！もう一度お試しください！'
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validateData = (object)$validator->validated();

                $startTime = Carbon::parse($validateData->start_time);

                $existAttendanceStart = AttendanceTime::where('user_id', $validateData->user_id)->whereDate('start_time', $startTime->format('Y-m-d'))->latest()->first();

                $existAttendanceEnd = AttendanceTime::where('user_id', $validateData->user_id)->whereDate('end_time', $startTime->format('Y-m-d'))->latest()->first();

                if (!empty($existAttendanceStart) && !empty($existAttendanceEnd)) {
                    Log::info("existAttendanceStartアウトおおおお", ["existAttendanceStart", $existAttendanceStart]);
                    return response()->json(
                        [
                            "message" => "既にに出勤時間が登録されています！"
                        ],
                        400,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header('Content-Type', 'application/json; charset=UTF-8');
                } else if (empty($existAttendanceStart) && empty($existAttendanceEnd)) {

                    // Base64データの取得
                    $base64Image = $request->input('start_photo_path');

                    // Base64データからヘッダーを除去し、$typeと$dataに分割します
                    list($type, $data) = explode(';', $base64Image);
                    list(, $data) = explode(',', $data);

                    // Base64データをデコード
                    $data = base64_decode($data);

                    // 保存するファイル名を生成
                    $fileName = 'startPhotos/' . uniqid() . '.jpg';

                    // ファイルを保存
                    Storage::disk('public')->put($fileName, $data);

                    $staff = Staff::where(
                        'user_id',
                        $validateData->user_id
                    )->first();

                    if (empty($staff)) {
                        $ownerId = Owner::where(
                            'user_id',
                            $validateData->user_id
                        )->value('id');
                    } else {
                        $ownerId = $staff->owner_id;
                    }


                    // リクエストから受け取ったデータを使用してレコードを作成
                    $attendanceTime = AttendanceTime::create([
                        'start_time' => $validateData->start_time,
                        'start_photo_path' =>  $fileName,
                        'user_id' => $validateData->user_id,
                    ]);


                    $attendanceTimesCacheKey = 'owner_' . $ownerId . 'staff_' . $validateData->user_id . 'attendanceTimes';

                    Cache::forget($attendanceTimesCacheKey);

                    $EditUser = User::find($validateData->user_id);

                    $EditUser->isAttendance = true;

                    $EditUser->save();


                    DB::commit();

                    return
                        response()->json(
                            [
                                "attendanceTime" => $attendanceTime,
                            ],
                            200,
                            [],
                            JSON_UNESCAPED_UNICODE
                        )->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                'message' => '出勤時間と写真の登録に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function pleaseEditEndTime(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {
                $validator = Validator::make($request->all(), [
                    'end_time' => 'required| date_format:Y-m-d H:i:s',
                    'end_photo_path' => 'required',
                    'user_id' => 'required|exists:users,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => '退勤時間登録に失敗しました！もう一度お試しください！'
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validateData = (object)$validator->validated();

                $endTime = Carbon::parse($validateData->end_time);

                $yesterday = $endTime->subDay()->format('Y-m-d');

                $existYesterdayStartTime = AttendanceTime::where('user_id', $validateData->user_id)->whereDate('start_time', $yesterday)->latest()->first();

                $existYesterdayStartTime->end_time = $validateData->end_time;

                $existYesterdayStartTime->end_photo_path = $validateData->end_photo_path;

                $existYesterdayStartTime->save();

                $staff = Staff::where('user_id', $validateData->user_id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $validateData->user_id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $attendanceTimesCacheKey = 'owner_' . $ownerId . 'staff_' . $validateData->user_id . 'attendanceTimes';

                Cache::forget($attendanceTimesCacheKey);
                $EditUser = User::find($validateData->user_id);

                $EditUser->isAttendance = false;

                $EditUser->save();


                DB::commit();

                return response()->json(
                    [
                        "message" => "昨日の退勤時間が登録されていませんので、オーナーまたは、マネージャーに報告してください！、その後出勤ボタンを押してください！",
                        "attendanceTime" => $existYesterdayStartTime,
                    ],
                    200,
                    [],
                    JSON_UNESCAPED_UNICODE
                )->header(
                    'Content-Type',
                    'application/json; charset=UTF-8'
                );
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '退勤時間と写真の登録に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function endTimeShot(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {
                $validator = Validator::make($request->all(), [
                    'end_time' => 'required| date_format:Y-m-d H:i:s',
                    'end_photo_path' => 'required',
                    'user_id' => 'required|exists:users,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => '退勤時間登録に失敗しました！もう一度お試しください！'
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validateData = (object)$validator->validated();

                $yesterday = Carbon::parse($validateData->end_time)->subDay()->format('Y-m-d');

                $existYesterdayStartTime = AttendanceTime::where('user_id', $validateData->user_id)->whereDate('start_time', $yesterday)->latest()->first();

                $existYesterdayEndTime = AttendanceTime::where('user_id', $validateData->user_id)->whereDate('end_time', $yesterday)->latest()->first();

                if (!empty($existYesterdayEndTime) && empty($existYesterdayStartTime)) {

                    // リクエストから受け取ったデータを使用してレコードを更新
                    $existYesterdayStartTime->end_time = "9999-12-31 23:59:59";

                    $existYesterdayEndTime->end_photo_path = "111222";

                    $existYesterdayStartTime->save();

                    $staff = Staff::where('user_id', $validateData->user_id)->first();

                    if (empty($staff)) {
                        $ownerId = Owner::where('user_id', $validateData->user_id)->value('id');
                    } else {
                        $ownerId = $staff->owner_id;
                    }

                    $attendanceTimesCacheKey = 'owner_' . $ownerId . 'staff_' . $validateData->user_id . 'attendanceTimes';

                    Cache::forget($attendanceTimesCacheKey);

                    $EditUser = User::find($validateData->user_id);

                    $EditUser->isAttendance = false;

                    $EditUser->save();


                    DB::commit();


                    return
                        response()->json(
                            [
                                "message" => "昨日の退勤時間が登録されていませんので、オーナーまたは、マネージャーに報告してください！、今は編集依頼を押した後に出勤ボタンを押してください！",
                                "attendanceTime" => $existYesterdayStartTime,
                            ],
                            200,
                            [],
                            JSON_UNESCAPED_UNICODE
                        )->header('Content-Type', 'application/json; charset=UTF-8');
                } else if (empty($existYesterdayEndTime) && empty($existYesterdayStartTime) || !empty($existYesterdayEndTime) && !empty($existYesterdayStartTime)) {

                    // Base64データの取得
                    $base64Image = $request->input('end_photo_path');

                    // Base64データからヘッダーを除去し、$typeと$dataに分割します
                    list($type, $data) = explode(';', $base64Image);
                    list(, $data) = explode(',', $data);

                    // Base64データをデコード
                    $data = base64_decode($data);

                    // 保存するファイル名を生成
                    $fileName = 'endPhotos/' . uniqid() . '.jpg';

                    // ファイルを保存
                    Storage::disk('public')->put($fileName, $data);

                    $today = Carbon::now()->format('Y-m-d');

                    $attendanceTime = AttendanceTime::where('user_id', $validateData->user_id)
                        ->whereDate('start_time', $today)
                        ->latest()
                        ->first();

                    $attendanceTime->end_time = $validateData->end_time;
                    $attendanceTime->end_photo_path = $fileName;

                    $attendanceTime->save();

                    $staff = Staff::where('user_id', $validateData->user_id)->first();

                    if (empty($staff)) {
                        $ownerId = Owner::where('user_id', $validateData->user_id)->value('id');
                    } else {
                        $ownerId = $staff->owner_id;
                    }

                    $attendanceTimesCacheKey = 'owner_' . $ownerId . 'staff_' . $validateData->user_id . 'attendanceTimes';

                    Cache::forget($attendanceTimesCacheKey);

                    $EditUser = User::find($validateData->user_id);

                    $EditUser->isAttendance = 0;

                    $EditUser->save();

                    $responseUser = $EditUser->only([
                        'id',
                        'name',
                        'isAttendance',
                    ]);

                    DB::commit();
                    return
                        response()->json(

                            [
                                "attendanceTime" => $attendanceTime,
                                "responseUser" => $responseUser
                            ],
                            200,
                            [],
                            JSON_UNESCAPED_UNICODE
                        )->header('Content-Type', 'application/json; charset=UTF-8');
                }
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '退勤時間と写真の登録に失敗しました！  
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function updateStartTime(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                // リクエストから受け取ったデータを使用してレコードを更新
                $attendanceTime = AttendanceTime::find($request->id);

                if ($attendanceTime->start_photo_path) {
                    Storage::disk('public')->delete($attendanceTime->start_photo_path);
                }

                // リクエストからデータを検証
                $validator = Validator::make($request->all(), [
                    'start_time' => 'required| date_format:Y-m-d H:i:s',
                    'start_photo_path' => 'required',
                    'user_id' => 'required|exists:users,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => '出勤時間登録に失敗しました！もう一度お試しください！'
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validateData = (object)$validator->validated();


                $attendanceTime->start_time = $validateData->start_time;
                $attendanceTime->start_photo_path =
                    "111222";

                $attendanceTime->save();

                $staff = Staff::where('user_id', $validateData->user_id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $validateData->user_id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }


                $attendanceTimesCacheKey = 'owner_' . $ownerId . 'staff_' . $validateData->user_id . 'attendanceTimes';


                Cache::forget($attendanceTimesCacheKey);

                DB::commit();

                return
                    response()->json(
                        [
                            "attendanceTime" => $attendanceTime
                        ],
                        200,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '出勤時間と写真の更新に失敗しました！
                もう一度お試しください！    '
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }

    public function updateEndTime(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                // リクエストから受け取ったデータを使用してレコードを更新
                $attendanceTime = AttendanceTime::find($request->id);

                if ($attendanceTime->end_photo_path) {
                    Storage::disk('public')->delete($attendanceTime->end_photo_path);
                }

                // リクエストからデータを検証
                $validator = Validator::make($request->all(), [
                    'end_time' => 'required|date_format:Y-m-d H:i:s',
                    'end_photo_path' => 'required',
                    'user_id' => 'required|exists:users,id',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => '退勤時間登録に失敗しました！もう一度お試しください！'
                    ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
                }

                $validateData = (object)$validator->validated();

                $attendanceTime->end_time = $validateData->end_time;
                $attendanceTime->end_photo_path = "111222";

                $attendanceTime->save();

                $staff = Staff::where('user_id', $validateData->user_id)->first();

                if (empty($staff)) {
                    $ownerId = Owner::where('user_id', $validateData->user_id)->value('id');
                } else {
                    $ownerId = $staff->owner_id;
                }

                $attendanceTimesCacheKey = 'owner_' . $ownerId . 'staff_' . $validateData->user_id . 'attendanceTimes';


                Cache::forget($attendanceTimesCacheKey);

                DB::commit();

                return
                    response()->json(
                        [
                            "attendanceTime" => $attendanceTime
                        ],
                        200,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '退勤時間と写真の更新に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }


    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::find(Auth::id());
            if ($user && $user->hasRole(Roles::$OWNER)) {
                Log::info("request", ["request", $request->id]);

                $userAttendance = AttendanceTime::find($request->id);

                $startFilePath = 'public/' . $userAttendance->start_photo_path;

                $endFilePath = 'public/' . $userAttendance->end_photo_path;

                // ファイルの存在を確認してから削除する
                if (Storage::exists($startFilePath)) {
                    Storage::delete($startFilePath);
                }

                if (Storage::exists($endFilePath)) {
                    Storage::delete($endFilePath);
                }

                // レコードを削除
                AttendanceTime::destroy($request->id);

                $ownerId = Owner::where('user_id', $user->id)->value('id');

                $attendanceTimesCacheKey = 'owner_' . $ownerId . 'staff_' . $request->id . 'attendanceTimes';

                Cache::forget($attendanceTimesCacheKey);

                DB::commit();

                return
                    response()->json(
                        [
                            "deleteId" => $request->id
                        ],
                        200,
                        [],
                        JSON_UNESCAPED_UNICODE
                    )->header('Content-Type', 'application/json; charset=UTF-8');
            } else {
                return response()->json([
                    'message' => 'あなたには権限がありません！',
                ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::alert("error", ["error", $e]);
            return response()->json([
                'message' => '勤怠時間の削除に失敗しました！
                もう一度お試しください！'
            ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
        }
    }
}
