<?php

namespace App\Http\Controllers;

use App\Enums\Permissions;
use Illuminate\Http\Request;
use App\Models\attendances;


class AttendancesController extends Controller
{

    // public function index()
    // {
    //     try {
    //         $attendances = attendances::all(); // または適切なクエリを使用してデータを取得する
    //         if ($attendances->isEmpty()) {
    //             return response()->json([
    //                 "resStatus" => "success",
    //                 "message" => "初めまして！新規登録ボタンからスタッフを登録してください！",
    //                 'attendances' => $attendances
    //             ], 200);
    //         } else {

    //             return response()->json([

    //                 'attendances' => $attendances
    //             ], 200);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             "message" => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'attendance_name' => 'required|string',
    //             'position' => 'required|string',
    //             'phone_number' => 'nullable|string',
    //             'address' => 'nullable|string',
    //             "isAttendance" => 'required|in:0,1',
    //         ]);

    //         $attendance = attendances::create([
    //             'attendance_name' => $validatedData['attendance_name'],
    //             'position' => $validatedData['position'],
    //             'phone_number' => $validatedData['phone_number'],
    //             'address' => $validatedData['address'],
    //             'isAttendance' => $validatedData['isAttendance'],
    //         ]);

    //         return
    //             response()->json(
    //                 [
    //                     "resStatus" => "success",
    //                     "attendance" => $attendance
    //                 ],
    //                 200
    //             );
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             "message" => "スタッフ情報登録時にエラーが発生しました。"
    //         ], 500);
    //     }
    // }

    // public function show($id)
    // {
    //     try {
    //         $attendance = attendances::find($id);

    //         if (!$attendance) {
    //             return response()->json([
    //                 'resStatus' => "error",
    //                 "message" => "スタッフが見つかりませんでした。"
    //             ], 400);
    //         }

    //         return response()->json([
    //             "resStatus" => "success",
    //             'attendance' => $attendance
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             "message" => "スタッフ情報取得時にエラーが発生しました。"
    //         ], 500);
    //     }
    // }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'attendance_name' => 'required|string',
    //             'position' => 'required|string',
    //             'phone_number' => 'nullable|string',
    //             'address' => 'nullable|string',
    //             'isAttendance' => 'required|in:0,1',
    //         ]);

    //         $attendance = attendances::find($id);

    //         $attendance->attendance_name = $validatedData['attendance_name'];
    //         $attendance->position = $validatedData['position'];
    //         $attendance->phone_number = $validatedData['phone_number'];
    //         $attendance->address = $validatedData['address'];
    //         $attendance->isAttendance = $validatedData['isAttendance'];


    //         $attendance->save();

    //         return response()->json(
    //             [
    //                 "resStatus" => "success",
    //                 "attendance" => $attendance
    //             ],
    //             200
    //         );
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             "message" => "スタッフ情報更新時にエラーが発生しました。"
    //         ], 500);
    //     }
    // }


    // public function destroy($id)
    // {
    //     try {
    //         $attendance = attendances::find($id);
    //         if (!$attendance) {
    //             return response()->json(["resStatus" => "error", 'message' => 'スタッフ情報が存在しません。'], 404);
    //         }
    //         $attendance->delete();
    //         return response()->json([
    //             "resStatus" => "success",
    //             "deleteId" => $id
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "resStatus" => "error",
    //             "message" => "スタッフ情報削除時にエラーが発生しました。"
    //         ], 500);
    //     }
    // }
}
