<?php

declare(strict_types=1);



namespace App\Services;

use App\Models\AttendanceTime;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceTimeService
{

  public function __construct()
  {
  }

  private function createCacheKey(int $ownerId, int $user_id): string
  {
    return 'owner_' . $ownerId . 'staff_' . $user_id . 'attendanceTimes';
  }

  public  function rememberCache(int $ownerId, int $user_id, string $yearMonth): Collection
  {
    try {
      if ($yearMonth !== "000111") {
        //わたってきた('Y-m')形式の年月を取得
        $currentYearAndMonth = $yearMonth;
      } else {
        // 現在の年月を取得 format('Y-m')で年月の形式に変換
        $currentYearAndMonth = Carbon::now()->format('Y-m');
      }
      $attendanceTimesCacheKey = $this->createCacheKey($ownerId, $user_id);

      $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

      $selectAttendanceTimes = Cache::remember($attendanceTimesCacheKey, $expirationInSeconds, function () use ($user_id, $currentYearAndMonth) {
        return AttendanceTime::where('user_id', $user_id)
          ->where('created_at', 'like', $currentYearAndMonth . '%')
          ->oldest('created_at')
          ->get();
      });

      $encodeAttendanceTimes =  $selectAttendanceTimes->transform(function ($item) {
        if ($item->start_photo_path)
          $item->start_photo_path = urlencode($item->start_photo_path);
        if ($item->end_photo_path)
          $item->end_photo_path = urlencode($item->end_photo_path);
        return $item;
      });

      return $encodeAttendanceTimes;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function forgetCache(int $ownerId, int $user_id): void
  {
    try {
      $attendanceTimesCacheKey = $this->createCacheKey($ownerId, $user_id);

      Cache::forget($attendanceTimesCacheKey);
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function createBase64Image(object $data, bool $startOrEnd): string
  {
    // Base64データの取得
    try {
      if ($startOrEnd) {
        $base64Image = $data->start_photo_path;
      } else {
        $base64Image = $data->end_photo_path;
      }

      // Base64データからヘッダーを除去し、$typeと$dataに分割します
      list($type, $data) = explode(';', $base64Image);
      list(, $data) = explode(',', $data);

      // Base64データをデコード
      $data = base64_decode($data);

      // 保存するファイル名を生成
      if ($startOrEnd) {
        $fileName = 'startPhotos/' . uniqid() . '.jpg';
      } else {
        $fileName = 'endPhotos/' . uniqid() . '.jpg';
      }
      // ファイルを保存
      Storage::disk('public')->put($fileName, $data);

      return $fileName;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function existAttendanceTime(
    string $time,
    bool $startOrEnd,
    int $user_id
  ): bool {
    try {
      if ($startOrEnd) {
        $startTime = Carbon::parse($time);
        $existAttendanceStart = AttendanceTime::where('user_id', $user_id)->whereDate('start_time', $startTime->format('Y-m-d'))->latest()->first();
        $existAttendanceEnd = AttendanceTime::where('user_id', $user_id)->whereDate('end_time', $startTime->format('Y-m-d'))->latest()->first();
        if (!empty($existAttendanceStart) && !empty($existAttendanceEnd)) {
          return  true;
        } else {
          return false;
        }
      } else {
        $yesterday = Carbon::parse($time)->subDay()->format('Y-m-d');
        $existYesterdayStartTime = AttendanceTime::where('user_id', $user_id)->whereDate('start_time', $yesterday)->latest()->first();

        $existYesterdayEndTime = AttendanceTime::where('user_id', $user_id)->whereDate('end_time', $yesterday)->latest()->first();
        if (empty($existYesterdayEndTime) && !empty($existYesterdayStartTime)) {
          return true;
        } else {
          return false;
        }
      }
    } catch (\Exception $e) {
      // Log::error($e->getMessage());
      abort(500, 'エラーが発生しました');
    }
  }

  private function attendanceTimePost(object $attendanceTime, array $data, bool $startOrEnd): AttendanceTime
  {
    try {
      if ($startOrEnd) {
        $attendanceTime->start_time = $data['start_time'];
        $attendanceTime->start_photo_path = $data['start_photo_path'];
      } else {
        $attendanceTime->end_time = $data['end_time'];
        $attendanceTime->end_photo_path = $data['end_photo_path'];
      }
      $attendanceTime->user_id = $data['user_id'];
      $attendanceTime->save();
      return $attendanceTime;
    } catch (\Exception $e) {
      // Log::error($e->getMessage());
      abort(500, 'エラーが発生しました');
    }
  }

  private function attendanceTimeStore(array $data, bool $startOrEnd): AttendanceTime
  {
    try {
      if ($startOrEnd) {
        $attendanceTime = new AttendanceTime();
        return $this->attendanceTimePost($attendanceTime, $data, true);
      } else {
        $today = Carbon::now()->format('Y-m-d');

        $attendanceTime = AttendanceTime::where('user_id', $data['user_id'])
          ->whereDate('start_time', $today)
          ->latest()
          ->first();

        return $this->attendanceTimePost($attendanceTime, $data, false);
      }
    } catch (\Exception $e) {
      // Log::error($e->getMessage());
      abort(500, 'エラーが発生しました');
    }
  }

  private function attendanceTimeUpdate(
    array $data,
    int $attendanceTimeId,
    bool $startOrEnd
  ): AttendanceTime {
    try {
      $attendanceTime = AttendanceTime::find($attendanceTimeId);
      if (empty($attendanceTime)) {
        abort(404, '在庫データが見つかりません');
      }
      if ($startOrEnd) {
        Storage::disk('public')->delete($attendanceTime->start_photo_path);
        $attendanceTime->start_photo_path = '111222';
      } else {
        Storage::disk('public')->delete($attendanceTime->end_photo_path);
        $attendanceTime->end_photo_path = '111222';
      }

      return $this->attendanceTimePost($attendanceTime, $data, $startOrEnd);
    } catch (\Exception $e) {
      // Log::error($e->getMessage());
      abort(500, 'エラーが発生しました');
    }
  }

  public function attendanceTimeValidateAndCreateOrUpdate(
    array $data,
    int|null $attendanceTimeId, //update時に必要。createでは不要
    bool $createOrUpdate, //true:create false:update
    bool $startOrEnd //true:start false:end
  ): AttendanceTime|JsonResponse {
    try {
      if ($startOrEnd) {
        $validator = Validator::make($data, [
          'start_time' => 'required|date_format:Y-m-d H:i:s',
          'start_photo_path' => 'required',
          'user_id' => 'required|exists:users,id',
        ]);
      } else {
        $validator = Validator::make($data, [
          'end_time' => 'required|date_format:Y-m-d H:i:s',
          'end_photo_path' => 'required',
          'user_id' => 'required|exists:users,id',
        ]);
      }

      if ($validator->fails()) {
        return response()->json(['message' => '入力内容を確認してください！'], 400);
      }

      $validatedData = $validator->validate();

      if ($createOrUpdate) {
        if ($startOrEnd) {
          $isExist = $this->existAttendanceTime($validatedData['start_time'], true, $validatedData['user_id']);
          if (!$isExist) {
            $fileName =  $this->createBase64Image((object) $validatedData, true);
            $validatedData['start_photo_path'] = $fileName;
            return $this->attendanceTimeStore($validatedData, true);
          } else {
            return response()->json(['message' => 'すでに出勤時間が登録されています！'], 400);
          }
        } else {
          $isYesterdayEndTimeOnlyEmpty = $this->existAttendanceTime($validatedData['end_time'], false, $validatedData['user_id']);
          if (!$isYesterdayEndTimeOnlyEmpty) {
            $fileName =  $this->createBase64Image((object) $validatedData, false);
            $validatedData['end_photo_path'] = $fileName;
            return $this->attendanceTimeStore($validatedData, false);
          } else {
            $validatedData['end_time'] = '9999-12-31 23:59:59';
            $validatedData['end_photo_path'] = '111222';
            $attendanceTime = $this->attendanceTimeStore($validatedData, false);
            return response()->json([
              "message" => "昨日の退勤時間が登録されていませんので、後ほどオーナーまたは、マネージャーに報告してください！、今は出勤ボタンを押して、出勤してください！",
              "attendanceTime" => $attendanceTime,
            ], 200);
          }
        }
      } else {
        if ($startOrEnd) {
          return $this->attendanceTimeUpdate($validatedData, $attendanceTimeId, true);
        } else {
          $validatedData['end_photo_path'] = '111222';
          return $this->attendanceTimeUpdate($validatedData, $attendanceTimeId, false);
        }
      }
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function attendanceTimeDelete(int $attendanceTimeId): void
  {
    try {
      $attendanceTime = AttendanceTime::find($attendanceTimeId);

      $startFilePath = 'public/' . $attendanceTime->start_photo_path;

      $endFilePath = 'public/' . $attendanceTime->end_photo_path;

      // ファイルの存在を確認してから削除する
      if (Storage::exists($startFilePath)) {
        Storage::delete($startFilePath);
      }
      if (Storage::exists($endFilePath)) {
        Storage::delete($endFilePath);
      }

      $attendanceTime->delete();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }
}
