<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Schedule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ScheduleService
{


  public function __construct()
  {
  }

  private function createCacheKey(int $ownerId): string
  {
    return 'owner_' . $ownerId . 'schedules';
  }

  public function rememberCache(int $ownerId): Collection
  //$isSelectがtrueの時はwhereBetweenは不要。一年分のデータを取得
  {
    try {
      $schedulesCacheKey =  $this->createCacheKey($ownerId);

      $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

      $year = Carbon::now()->year;
      $schedules = Cache::remember($schedulesCacheKey, $expirationInSeconds, function () use ($ownerId, $year) {

        $yearStart = Carbon::create($year, 1, 1);
        $nextYearEnd = Carbon::create($year + 1, 12, 31); // 次の年の最終日

        return Schedule::where('owner_id', $ownerId)
          ->whereBetween('start_time', [$yearStart, $nextYearEnd])
          ->get();
      });

      return $schedules;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function forgetCache(int $ownerId): void
  {
    try {
      $schedulesCacheKey = $this->createCacheKey($ownerId);

      Cache::forget($schedulesCacheKey);
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }


  private function scheduleStore(array $data, int $ownerId): Schedule
  {
    try {
      $schedule = new Schedule();
      $schedule->title = $data['title'];
      $schedule->start_time = $data['start_time'];
      $schedule->end_time = $data['end_time'];
      $schedule->allDay = $data['allDay'];
      $schedule->customer_id = $data['customer_id'] ?? null;
      $schedule->owner_id = $ownerId;
      $schedule->save();
      // Log::info('スケジュール作成成功', ['schedule' => $schedule]);

      return $schedule;
    } catch (\Exception $e) {
      // Log::error('スケジュール作成失敗', [$e->getMessage()]);
      abort(500, 'エラーが発生しました');
    }
  }

  private function scheduleUpdate(array $data, int $scheduleId): Schedule
  {
    try {
      $schedule = Schedule::find($scheduleId);
      $schedule->title = $data['title'];
      $schedule->start_time = $data['start_time'];
      $schedule->end_time = $data['end_time'];
      $schedule->allDay = $data['allDay'];
      $schedule->customer_id = $data['customer_id'] ?? null;
      $schedule->save();

      // Log::info('スケジュール更新成功', ['schedule' => $schedule]);

      return $schedule;
    } catch (\Exception $e) {
      // Log::error($e->getMessage());
      // Log::error('スケジュール更新失敗', [$e->getMessage()]);
      abort(500, 'エラーが発生しました');
    }
  }

  public function scheduleValidateAndCreateOrUpdate(
    array $data,
    int $ownerId,
    int|null $scheduleId,
    bool $createOrUpdate,
    bool $isCustomer
  )
  // request->all()を受け取り、バリデーションを行い、createOrUpdateがtrueの場合はowner_idを受け取り新規作成、falseの場合はschedule_idを受け取り更新を行う。
  {
    try {
      if ($isCustomer) {
        // Log::info('スケジュールバリデーション開始isCustomer', ['schedule' => $data]);

        $validator = Validator::make($data, [
          'title' => 'nullable',
          'start_time' => 'required|date_format:Y-m-d H:i:s',
          'end_time' => 'required|date_format:Y-m-d H:i:s',
          'allDay' => 'required|boolean',
          'customer_id' => 'required',
        ]);
      } else {
        // Log::info('スケジュールバリデーション開始', ['schedule' => $data]);

        $validator = Validator::make($data, [
          'title' => 'required|string',
          'start_time' => 'required|date_format:Y-m-d H:i:s',
          'end_time' => 'required|date_format:Y-m-d H:i:s',
          'allDay' => 'required|boolean',
        ]);
      }

      if ($validator->fails()) {
        // Log::error('スケジュール更新失敗バリデーションエラー', ['schedule' => $data]);
        throw new HttpException(403, '入力内容が正しくありません');
      }

      $validatedData = $validator->validate();
      // Log::info('スケジュールバリデーション成功', ['schedule' => $validatedData]);

      if ($createOrUpdate) {
        $schedule = $this->scheduleStore($validatedData, $ownerId);
        // Log::info('スケジュール新規作成成功', ['schedule' => $schedule]);
        return $schedule;
      } else {
        $schedule = $this->scheduleUpdate($validatedData, $scheduleId);
        // Log::info('スケジュール更新成功', ['schedule' => $schedule]);
        return $schedule;
      }
    } catch (\Exception $e) {
      // Log::error($e->getMessage());
      abort(500, 'エラーが発生しました');
    }
  }

  public  function scheduleDelete(int $scheduleId): void
  {
    try {
      $schedule = Schedule::find($scheduleId);

      $schedule->delete();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }
}
