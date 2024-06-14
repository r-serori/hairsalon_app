<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\attendance_times;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JSON;

class AttendanceTimesController extends Controller
{

    public function selectedAttendanceTime($id, $yearMonth)
    {
        try {
            if ($yearMonth !== "無し") {
                $currentYearAndMonth = $yearMonth;
            } else {
                $currentYearAndMonth = Carbon::now()->format('Y-m');
            }

            $selectAttendanceTimes = attendance_times::where('user_id', $id)->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentYearAndMonth])
                ->get();

            // 各レコードのstart_photo_pathとend_photos_pathをエンコード
            $selectAttendanceTimes->transform(function ($item) {
                $item->start_photo_path = urlencode($item->start_photo_path);
                $item->end_photo_path = urlencode($item->end_photo_path);
                return $item;
            });

            if ($selectAttendanceTimes->isEmpty() && $yearMonth === "無し") {
                return response()->json([
                    "resStatus" => "success",
                    "message" => "初めまして！勤怠画面から出勤してください！",
                    'attendanceTimes' => $selectAttendanceTimes
                ], 200);
            } else if ($selectAttendanceTimes->isEmpty() && $yearMonth !== "無し") {
                return response()->json([
                    "resStatus" => "success",
                    "message" => "選択した勤怠履歴がありません。",
                    'attendanceTimes' => $selectAttendanceTimes
                ], 200);
            } else {
                return response()->json([
                    "resStatus" => "success",
                    'attendanceTimes' => $selectAttendanceTimes
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '勤怠時間の取得に失敗しました。'
            ], 500);
        }
    }

    public function firstAttendanceTime($id)
    {
        try {
            $firstAttendanceTime = attendance_times::where('user_id', $id)->latest('created_at')->first();

            return response()->json([
                "resStatus" => "success",
                'attendanceTime' => $firstAttendanceTime,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '勤怠時間の取得に失敗しました。'
            ], 500);
        }
    }


    public function startPhotos($fileName)
    {
        try {
            // URLエンコードされたファイル名をデコード
            $decoFileName = urldecode($fileName);

            $imagePath = storage_path('app/public/' . $decoFileName);

            Log::info("imagePath", ["imagePath", $imagePath]);

            // 画像ファイルが存在するか確認
            if (!file_exists($imagePath)) {
                abort(404);
            }

            return response()->file($imagePath);
        } catch (\Exception $e) {
            abort(404);
        }
    }


    public function endPhotos($fileName)
    {
        try {

            // URLエンコードされたファイル名をデコード
            $decoFileName = urldecode($fileName);

            $imagePath = storage_path('app/public/' . $decoFileName);

            dd($imagePath);


            // 画像ファイルが存在するか確認
            if (!file_exists($imagePath)) {
                abort(404);
            }

            return response()->file($imagePath);
        } catch (\Exception $e) {
            abort(404);
        }
    }


    public function startTimeShot(Request $request)
    {
        try {
            $startTime = Carbon::parse($request->start_time);

            $existAttendanceStart = attendance_times::where('user_id', $request->user_id)->whereDate('start_time', $startTime->format('Y-m-d'))->latest()->first();
            $existAttendanceEnd = attendance_times::where('user_id', $request->user_id)->whereDate('end_time', $startTime->format('Y-m-d'))->latest()->first();

            if (!empty($existAttendanceStart) && !empty($existAttendanceEnd)) {
                return response()->json(
                    [
                        "resStatus" => "error",
                        "message" => "既にに出勤時間が登録されています。"
                    ],
                    500,
                    [],
                    JSON_UNESCAPED_UNICODE
                );
            } else if (empty($existAttendanceStart) && empty($existAttendanceEnd)) {

                // リクエストからデータを検証
                $request->validate([
                    'start_time' => 'required',
                    'start_photo_path' => 'required',
                    'user_id' => 'required|exists:users,id',
                ]);
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

                // リクエストから受け取ったデータを使用してレコードを作成
                $attendanceTime = attendance_times::create([
                    'start_time' => $request->start_time,
                    'start_photo_path' =>  $fileName,
                    'user_id' => $request->user_id,
                ]);


                $user = User::find($request->user_id);

                $user->isAttendance = 1;

                $user->save();

                return
                    response()->json(
                        [
                            "resStatus" => "success",
                            "attendance" => $user,
                            "attendanceTime" => $attendanceTime,
                        ],
                        200
                    );
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '出勤時間と写真の登録に失敗しました。'
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function pleaseEditEndTime(Request $request)
    {
        try {
            $request->validate([
                'end_time' => 'required',
                'end_photo_path' => 'required',
                'user_id' => 'required|exists:users,id',
            ]);

            $endTime = Carbon::parse($request->end_time);

            $yesterday = $endTime->subDay()->format('Y-m-d');

            $existYesterdayStartTime = attendance_times::where('user_id', $request->user_id)->whereDate('start_time', $yesterday)->latest()->first();

            $existYesterdayStartTime->end_time = $request->end_time;

            $existYesterdayStartTime->end_photo_path = $request->end_photo_path;

            $existYesterdayStartTime->save();

            $user = User::find($request->user_id);

            $user->isAttendance = 0;

            $user->save();

            return response()->json(
                [
                    "resStatus" => "success",
                    "message" => "昨日の退勤時間が登録されていませんので、オーナーまたは、マネージャーに報告してください！、その後出勤ボタンを押してください！",
                    "attendanceTime" => $existYesterdayStartTime, "attendance" => $user
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }


    public function endTimeShot(Request $request)
    {

        try {
            $request->validate([
                'end_time' => 'required',
                'end_photo_path' => 'required',
                'user_id' => 'required|exists:users,id',
            ]);
            $endTime = Carbon::parse($request->end_time);

            $yesterday = $endTime->subDay()->format('Y-m-d');

            $existYesterdayStartTime = attendance_times::where('user_id', $request->user_id)->whereDate('start_time', $yesterday)->latest()->first();

            $existYesterdayEndTime = attendance_times::where('user_id', $request->user_id)->whereDate('end_time', $yesterday)->latest()->first();

            if (!empty($existYesterdayEndTime) && empty($existYesterdayStartTime)) {

                // リクエストから受け取ったデータを使用してレコードを更新
                $existYesterdayStartTime->end_time = "9999-12-31 23:59:59";

                $existYesterdayEndTime->end_photo_path = "編集済み";

                $existYesterdayStartTime->save();

                $user = User::find($request->user_id);

                $user->isAttendance = 0;

                $user->save();
                return
                    response()->json(
                        [
                            "resStatus" => "success",
                            "message" => "昨日の退勤時間が登録されていませんので、オーナーまたは、マネージャーに報告してください！、今は編集依頼を押した後に出勤ボタンを押してください！",
                            "attendanceTime" => $existYesterdayStartTime, "attendance" => $user
                        ],
                        200
                    );
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

                $attendanceTime = attendance_times::where('user_id', $request->user_id)
                    ->whereDate('start_time', $today)
                    ->latest()
                    ->first();

                $attendanceTime->end_time = $request->end_time;
                $attendanceTime->end_photo_path = $fileName;

                $attendanceTime->save();

                $user = User::find($request->user_id);

                $user->isAttendance = 0;

                $user->save();

                return
                    response()->json(
                        ["resStatus" => "success", "attendanceTime" => $attendanceTime, "attendance" => $user],
                        200
                    );
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function updateStartTime(Request $request, $id)
    {
        try {
            // リクエストから受け取ったデータを使用してレコードを更新
            $attendanceTime = attendance_times::find($id);

            if ($attendanceTime->start_photo_path) {
                Storage::disk('public')->delete($attendanceTime->start_photo_path);
            }

            // リクエストからデータを検証
            $request->validate([
                'start_time' => 'required',
                'start_photo_path' => 'required',
                'user_id' => 'required|exists:users,id',
            ]);

            $startTime = Carbon::parse($request->start_time);

            $attendanceTime->start_time = $request->start_time;
            $attendanceTime->start_photo_path =
                "編集済み";

            $attendanceTime->save();

            return
                response()->json(
                    ["resStatus" => "success", "attendanceTime" => $attendanceTime],
                    200
                );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '出勤時間と写真の更新に失敗しました。'
            ], 500);
        }
    }

    public function updateEndTime(Request $request, $id)
    {
        try {
            // リクエストから受け取ったデータを使用してレコードを更新
            $attendanceTime = attendance_times::find($id);

            if ($attendanceTime->end_photo_path) {
                Storage::disk('public')->delete($attendanceTime->end_photo_path);
            }

            // リクエストからデータを検証
            $request->validate([
                'end_time' => 'required',
                'end_photo_path' => 'required',
                'user_id' => 'required|exists:users,id',
            ]);

            $endTime = Carbon::parse($request->end_time);

            $attendanceTime->end_time = $request->end_time;
            $attendanceTime->end_photo_path =
                "編集済み";

            $attendanceTime->save();

            return
                response()->json(
                    ["resStatus" => "success", "attendanceTime" => $attendanceTime],
                    200
                );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '退勤時間と写真の更新に失敗しました。'
            ], 500);
        }
    }


    public function destroy($id)
    {

        try {
            $user = attendance_times::find($id);

            if ($user->end_photo_path) {
                Storage::disk('public')->delete($user->end_photo_path);
            }

            if ($user->start_photo_path) {
                Storage::disk('public')->delete($user->start_photo_path);
            }

            // レコードを削除
            attendance_times::destroy($id);

            // 削除後に index 画面にリダイレクトする

            return
                response()->json(
                    [
                        "resStatus" => "success",
                        "deleteId" => $id
                    ],
                    200
                );
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '勤怠時間の削除に失敗しました。'
            ], 500);
        }
    }
}
