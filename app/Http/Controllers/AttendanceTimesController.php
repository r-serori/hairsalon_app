<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\attendance_times;
use App\Models\attendances;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceTimesController extends Controller
{

    public function selectedAttendanceTime($id)
    {
        try {
            $selectAttendanceTimes = attendance_times::where('attendance_id', $id)->get();

            // 各レコードのstart_photo_pathとend_photo_pathをエンコード
            $selectAttendanceTimes->transform(function ($item) {
                $item->start_photo_path = urlencode($item->start_photo_path);
                $item->end_photo_path = urlencode($item->end_photo_path);
                return $item;
            });

            return response()->json([
                "resStatus" => "success",
                'attendanceTimes' => $selectAttendanceTimes
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

            $existAttendance = attendance_times::where('attendance_id', $request->attendance_id)->whereDate('start_time', $startTime->format('Y-m-d'))->get();

            if ($existAttendance->count() > 0) {
                return
                    response()->json(
                        ["resStatus" => "error", "message" => "既にに出勤時間が登録されています。"],
                        500
                    );
            } else {

                // リクエストからデータを検証
                $request->validate([
                    'start_time' => 'required',
                    'start_photo_path' => 'required',
                    'attendance_id' => 'required|exists:attendances,id',
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
                    'attendance_id' => $request->attendance_id,
                ]);


                $attendance = attendances::find($request->attendance_id);

                $attendance->isAttendance = 1;

                $attendance->save();

                return
                    response()->json(
                        [
                            "resStatus" => "success",
                            "attendance" => $attendance,
                            "attendanceTime" => $attendanceTime,
                        ],
                        200
                    );
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '出勤時間と写真の登録に失敗しました。'
            ], 500);
        }
    }


    public function endTimeShot(Request $request)
    {
        try {
            $endTime = Carbon::parse($request->end_time);

            $existAttendance = attendance_times::where('attendance_id', $request->attendance_id)->whereDate('end_time', $endTime->format('Y-m-d'))->get();

            $existStartTime = attendance_times::where('attendance_id', $request->attendance_id)->whereDate('start_time', $endTime->format('Y-m-d'))->get();

            if ($existAttendance->count() > 0) {
                return
                    response()->json(
                        ["resStatus" => "error", "message" => "すでに退勤時間が登録されています。"],
                        500
                    );
            } else if ($existStartTime->count() == 0) {
                return
                    response()->json(
                        ["resStatus" => "error", "message" => "出勤時間が登録されていません。"],
                        500
                    );
            } else {
                // リクエストからデータを検証
                $request->validate([
                    'end_time' => 'required',
                    'end_photo_path' => 'required',
                    'attendance_id' => 'required|exists:attendances,id',
                ]);

                $endTime = Carbon::parse($request->end_time);

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

                $attendanceTime = attendance_times::where('attendance_id', $request->attendance_id)
                    ->whereDate('start_time', $endTime->format('Y-m-d'))
                    ->latest()
                    ->first();
                // リクエストから受け取ったデータを使用してレコードを更新
                $attendanceTime->end_time = $request->end_time;
                $attendanceTime->end_photo_path = $fileName;

                $attendanceTime->save();


                $attendance = attendances::find($request->attendance_id);

                $attendance->isAttendance = 0;

                $attendance->save();

                return
                    response()->json(
                        ["resStatus" => "success", "attendanceTime" => $attendanceTime, "attendance" => $attendance],
                        200
                    );
            }
        } catch (\Exception $e) {
            return response()->json([
                "resStatus" => "error",
                'message' => '退勤時間と写真の登録に失敗しました。'
            ], 500);
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
                'attendance_id' => 'required|exists:attendances,id',
            ]);

            $startTime = Carbon::parse($request->start_time);

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



            $attendanceTime->start_time = $request->start_time;
            $attendanceTime->start_photo_path = $fileName;

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
                'attendance_id' => 'required|exists:attendances,id',
            ]);

            $endTime = Carbon::parse($request->end_time);

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

            $attendanceTime->end_time = $request->end_time;
            $attendanceTime->end_photo_path = $fileName;

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
            $attendance = attendance_times::find($id);

            if ($attendance->end_photo_path) {
                Storage::disk('public')->delete($attendance->end_photo_path);
            }

            if ($attendance->start_photo_path) {
                Storage::disk('public')->delete($attendance->start_photo_path);
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
