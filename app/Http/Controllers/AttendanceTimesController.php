<?php

declare(strict_types=1);

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
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\AttendanceTimeService;
use Illuminate\Http\JsonResponse;

class AttendanceTimesController extends BaseController
{
    protected $getImportantIdService;
    protected $attendanceTimeService;
    protected $hasRole;

    public function __construct(GetImportantIdService $getImportantIdService, AttendanceTimeService $attendanceTimeService, HasRole $hasRole)
    {
        $this->getImportantIdService = $getImportantIdService;
        $this->attendanceTimeService = $attendanceTimeService;
        $this->hasRole = $hasRole;
    }

    //クエリのuser_idを受け取り、そのユーザーの勤怠時間を１か月分取得　Gate,ALL
    //yearMonthが"000111"の場合は当月の勤怠時間を取得
    public function selectedAttendanceTime($yearMonth, $id)
    {
        try {
            $user = $this->hasRole->ownerAllow();

            $user_id = intval(urldecode($id));
            $yearMonth = urldecode($yearMonth);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $selectAttendanceTimes = $this->attendanceTimeService->rememberCache($ownerId, $user_id, $yearMonth);

            if ($selectAttendanceTimes->isEmpty() && $yearMonth === "000111") {
                return $this->responseMan([
                    "message" => "初めまして！勤怠画面から出勤してください！",
                    'attendanceTimes' => []
                ]);
            } else if ($selectAttendanceTimes->isEmpty() && $yearMonth !== "000111") {
                return $this->responseMan([
                    "message" => "選択した勤怠履歴がありません！",
                    'attendanceTimes' => []
                ]);
            } else {
                return $this->responseMan([
                    'attendanceTimes' => $selectAttendanceTimes,
                ]);
            }
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->responseMan([
                'message' => '勤怠時間の取得に失敗しました！もう一度お試しください！'
            ], 500);
        }
    }

    public function startTimeShot(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->hasRole->allAllow();

            $attendanceTime = $this->attendanceTimeService->attendanceTimeValidateAndCreateOrUpdate($request->all(), null, true, true);

            if ($attendanceTime instanceof JsonResponse) {
                return $attendanceTime;
            }

            $ownerId = $this->getImportantIdService->getOwnerId($attendanceTime->user_id);

            $this->attendanceTimeService->forgetCache($ownerId, $attendanceTime->user_id);

            $EditUser = User::find($attendanceTime->user_id);
            $EditUser->isAttendance = true;
            $EditUser->save();

            DB::commit();

            return
                $this->responseMan([
                    "attendanceTime" => $attendanceTime,
                ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                'message' => '出勤時間と写真の登録に失敗しました！もう一度お試しください！'
            ], 500);
        }
    }

    // public function pleaseEditEndTime(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $user = User::find(Auth::id());
    //         if ($user && $user->hasRole(Roles::$OWNER) || $user->hasRole(Roles::$MANAGER) || $user->hasRole(Roles::$STAFF)) {
    //             $validator = Validator::make($request->all(), [
    //                 'end_time' => 'required| date_format:Y-m-d H:i:s',
    //                 'end_photo_path' => 'required',
    //                 'user_id' => 'required|exists:users,id',
    //             ]);

    //             if ($validator->fails()) {
    //                 return response()->json([
    //                     'message' => '退勤時間登録に失敗しました！もう一度お試しください！'
    //                 ], 400, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //             }

    //             $validateData = (object)$validator->validated();

    //             $endTime = Carbon::parse($validateData->end_time);

    //             $yesterday = $endTime->subDay()->format('Y-m-d');

    //             $existYesterdayStartTime = AttendanceTime::where('user_id', $validateData->user_id)->whereDate('start_time', $yesterday)->latest()->first();

    //             $existYesterdayStartTime->end_time = $validateData->end_time;

    //             $existYesterdayStartTime->end_photo_path = $validateData->end_photo_path;

    //             $existYesterdayStartTime->save();

    //             } else {

    //             $attendanceTimesCacheKey = 'owner_' . $ownerId . 'staff_' . $validateData->user_id . 'attendanceTimes';

    //             Cache::forget($attendanceTimesCacheKey);
    //             $EditUser = User::find($validateData->user_id);

    //             $EditUser->isAttendance = false;

    //             $EditUser->save();


    //             DB::commit();

    //             return response()->json(
    //                 [
    //                     "message" => "昨日の退勤時間が登録されていませんので、オーナーまたは、マネージャーに報告してください！、その後出勤ボタンを押してください！",
    //                     "attendanceTime" => $existYesterdayStartTime,
    //                 ],
    //                 200,
    //                 [],
    //                 JSON_UNESCAPED_UNICODE
    //             )->header(
    //                 'Content-Type',
    //                 'application/json; charset=UTF-8'
    //             );
    //         } else {
    //             return response()->json([
    //                 'message' => 'あなたには権限がありません！',
    //             ], 403, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //         }
    //     } catch (\Exception $e) {
    // Log::error($e->getMessage())
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => '退勤時間と写真の登録に失敗しました！
    //             もう一度お試しください！'
    //         ], 500, [], JSON_UNESCAPED_UNICODE)->header('Content-Type', 'application/json; charset=UTF-8');
    //     }
    // }


    public function endTimeShot(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->hasRole->allAllow();
            $attendanceTime =  $this->attendanceTimeService->attendanceTimeValidateAndCreateOrUpdate($request->all(), null, true, false);


            $ownerId = $this->getImportantIdService->getOwnerId($request->user_id);

            $this->attendanceTimeService->forgetCache($ownerId, $request->user_id);

            $editUser = User::find($request->user_id);

            $editUser->isAttendance = false;

            $editUser->save();

            DB::commit();

            if ($attendanceTime instanceof AttendanceTime) {
                return
                    $this->responseMan([
                        "attendanceTime" => $attendanceTime,
                    ]);
            } else {
                return $attendanceTime;
            }
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                'message' => '退勤時間と写真の登録に失敗しました！もう一度お試しください！'
            ], 500);
        }
    }

    public function updateStartTime(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();
            // リクエストから受け取ったデータを使用してレコードを更新

            $attendanceTime = $this->attendanceTimeService->attendanceTimeValidateAndCreateOrUpdate($request->all(), $request->id, false, true);


            $ownerId = $this->getImportantIdService->getOwnerId($request->user_id);

            $this->attendanceTimeService->forgetCache($ownerId, $request->user_id);

            DB::commit();

            return
                $this->responseMan([
                    "attendanceTime" => $attendanceTime,
                ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                'message' => '出勤時間と写真の更新に失敗しました！もう一度お試しください！'
            ], 500);
        }
    }

    public function updateEndTime(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();
            // リクエストから受け取ったデータを使用してレコードを更新
            $attendanceTime = $this->attendanceTimeService->attendanceTimeValidateAndCreateOrUpdate($request->all(), $request->id, false, false);


            $ownerId = $this->getImportantIdService->getOwnerId($request->user_id);

            $this->attendanceTimeService->forgetCache($ownerId, $request->user_id);

            DB::commit();

            return
                $this->responseMan([
                    "attendanceTime" => $attendanceTime,
                ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                'message' => '退勤時間と写真の更新に失敗しました！もう一度お試しください！'
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->hasRole->ownerAllow();

            $this->attendanceTimeService->attendanceTimeDelete($request->id);

            $ownerId = $this->getImportantIdService->getOwnerId($request->id);

            $this->attendanceTimeService->forgetCache($ownerId, $request->id);

            DB::commit();

            return $this->responseMan([
                "deleteId" => $request->id
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                'message' => '勤怠時間の削除に失敗しました！もう一度お試しください！'
            ], 500);
        }
    }
}
