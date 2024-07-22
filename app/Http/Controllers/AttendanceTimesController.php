<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceTime;
use App\Models\User;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\AttendanceTimeService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
            $user = $this->hasRole->ownerAllow(); // ユーザー情報を取得&権限確認

            $user_id = intval(urldecode($id));
            $yearMonth = urldecode($yearMonth);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $selectAttendanceTimes = $this->attendanceTimeService->rememberCache($ownerId, $user_id, $yearMonth); //キャッシュから取得

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
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
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
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }

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
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }

    public function updateStartTime(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->hasRole->ownerAllow();
            // リクエストから受け取ったデータを使用してレコードを更新
            $attendanceTime = $this->attendanceTimeService->attendanceTimeValidateAndCreateOrUpdate($request->all(), $request->id, false, true);

            $ownerId = $this->getImportantIdService->getOwnerId($request->user_id);

            $this->attendanceTimeService->forgetCache($ownerId, $request->user_id);

            DB::commit();

            return
                $this->responseMan([
                    "attendanceTime" => $attendanceTime,
                ]);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
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
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
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
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }
}
