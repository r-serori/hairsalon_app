<?php

namespace App\Services;

use App\Models\DailySale;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DailySaleService
{

  public function __construct()
  {
  }

  private function createCacheKey(int $ownerId): string
  {
    return 'owner_' . $ownerId . 'dailySales';
  }

  public function getDailySales(int $ownerId, int $currentYear): Collection
  {
    try {
      $currentYearStart = Carbon::create($currentYear, 1, 1);
      $currentYearEnd = Carbon::create($currentYear, 12, 31); // 次の年の最終日

      return DailySale::where('owner_id', $ownerId)
        ->whereBetween('date', [$currentYearStart, $currentYearEnd])->oldest('date')->get();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function rememberCache(int $ownerId, int $currentYear): Collection
  {
    try {
      $dailySalesCacheKey = $this->createCacheKey($ownerId);
      $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

      $daily_sales = Cache::remember($dailySalesCacheKey, $expirationInSeconds, function () use ($ownerId, $currentYear) {
        return $this->getDailySales($ownerId, $currentYear);
      });

      return $daily_sales;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function forgetCache(int $ownerId): void
  {
    try {
      $dailySalesCacheKey = $this->createCacheKey($ownerId);

      Cache::forget($dailySalesCacheKey);
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function dailySaleStore(array $data, int $ownerId): DailySale
  {
    try {
      $existDailySale = DailySale::where('owner_id', $ownerId)->whereDate('date', $data['date'])->first();

      if ($existDailySale) {
        abort(400, 'その日の日次売上は既に存在しています！日次売上画面から編集をして数値を変更するか、削除してもう一度この画面から更新してください！');
      }

      $dailySale = new DailySale();
      $dailySale->date = $data['date'];
      $dailySale->daily_sales = $data['daily_sales'];
      $dailySale->owner_id = $ownerId;
      $dailySale->save();

      return $dailySale;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function dailySaleUpdate(array $data, int $dailySaleId): DailySale
  {
    try {
      $dailySale = DailySale::find($dailySaleId);
      $dailySale->date = $data['date'];
      $dailySale->daily_sales = $data['daily_sales'];
      $dailySale->save();

      return $dailySale;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function dailySaleValidateAndCreateOrUpdate(
    array $data,
    int $dailySaleIdOrOwnerId,
    bool $createOrUpdate
  ): DailySale {
    try {
      $validator = Validator::make($data, [
        'date' => 'required|date_format:Y-m-d',
        'daily_sales' => 'required|integer|min:0',
      ]);

      if ($validator->fails()) {
        throw new HttpException(403, '入力内容が正しくありません');
      }
      $validatedDate = $validator->validate();

      if ($createOrUpdate) {
        $ownerId = $dailySaleIdOrOwnerId;
        return $this->dailySaleStore($validatedDate, $ownerId);
      } else {
        $dailySaleId = $dailySaleIdOrOwnerId;
        return $this->dailySaleUpdate($validatedDate, $dailySaleId);
      }
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function dailySaleDelete(int $dailySaleId): void
  {
    try {
      $dailySale = DailySale::find($dailySaleId);

      $dailySale->delete();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }
}
