<?php

namespace App\Services;

use App\Models\MonthlySale;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MonthlySaleService
{

  public function __construct()
  {
  }

  private function createCacheKey(int $ownerId): string
  {
    return 'owner_' . $ownerId . 'monthlySales';
  }

  public function getMonthlySales(int $ownerId, int $currentYear): Collection
  {
    try {
      $currentYearStart = Carbon::create($currentYear, 1, 1)->format('Y-m');
      $currentYearEnd = Carbon::create($currentYear, 12, 31)->format('Y-m');
      return MonthlySale::where('owner_id', $ownerId)->whereBetween('year_month', [$currentYearStart, $currentYearEnd])->oldest('year_month')->get();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }


  public  function rememberCache(int $ownerId, int $currentYear): Collection
  {
    try {
      $monthlySalesCacheKey = $this->createCacheKey($ownerId);

      $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

      // 月別売上一覧を取得
      $monthly_sales = Cache::remember($monthlySalesCacheKey, $expirationInSeconds, function () use ($ownerId, $currentYear) {
        return $this->getMonthlySales($ownerId, $currentYear);
      });

      return $monthly_sales;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function forgetCache(int $ownerId): void
  {
    try {
      $monthlySalesCacheKey = $this->createCacheKey($ownerId);

      Cache::forget($monthlySalesCacheKey);
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function monthlySaleStore(array $data, int $ownerId): MonthlySale
  {
    try {
      $existMonthlySale = MonthlySale::where('owner_id', $ownerId)->where('year_month', $data['year_month'])->first();

      if ($existMonthlySale) {
        abort(400, 'その日の月次売上は既に存在しています！月次売上画面から編集をして数値を変更するか、削除してもう一度この画面から更新してください！');
      }

      $monthlySale = new MonthlySale();
      $monthlySale->year_month = $data['year_month'];
      $monthlySale->monthly_sales = $data['monthly_sales'];
      $monthlySale->owner_id = $ownerId;
      $monthlySale->save();

      return $monthlySale;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function monthlySaleUpdate(array $data, int $monthlySaleId): MonthlySale
  {
    try {
      $monthlySale = MonthlySale::find($monthlySaleId);
      $monthlySale->year_month = $data['year_month'];
      $monthlySale->monthly_sales = $data['monthly_sales'];
      $monthlySale->save();

      return $monthlySale;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function monthlySaleValidateAndCreateOrUpdate(
    array $data,
    int $monthlySaleIdOrOwnerId,
    bool $createOrUpdate
  ): MonthlySale {
    try {
      $validator = Validator::make($data, [
        'year_month' => 'required|date_format:Y-m',
        'monthly_sales' => 'required|integer|min:0',
      ]);

      if ($validator->fails()) {
        throw new HttpException(403, '入力内容が正しくありません');
      }
      $validatedDate = $validator->validate();

      if ($createOrUpdate) {
        $ownerId = $monthlySaleIdOrOwnerId;
        return $this->monthlySaleStore($validatedDate, $ownerId);
      } else {
        $monthlySaleId = $monthlySaleIdOrOwnerId;
        return $this->monthlySaleUpdate($validatedDate, $monthlySaleId);
      }
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function monthlySaleDelete(int $monthlySaleId): void
  {
    try {
      $monthlySale = MonthlySale::find($monthlySaleId);

      $monthlySale->delete();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }
}
