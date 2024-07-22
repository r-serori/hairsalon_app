<?php

namespace App\Services;

use App\Models\YearlySale;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;


class YearlySaleService
{

  public function __construct()
  {
  }

  private function createCacheKey(int $ownerId): string
  {
    return 'owner_' . $ownerId . 'yearlySales';
  }

  public  function rememberCache(int $ownerId): Collection
  {
    try {
      $yearlySalesCacheKey = $this->createCacheKey($ownerId);

      $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

      // 月別売上一覧を取得
      $yearly_sales = Cache::remember($yearlySalesCacheKey, $expirationInSeconds, function () use ($ownerId) {
        return YearlySale::where('owner_id', $ownerId)->oldest('year')->get();
      });

      return $yearly_sales;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function forgetCache(int $ownerId): void
  {
    try {
      $yearlySalesCacheKey = $this->createCacheKey($ownerId);

      Cache::forget($yearlySalesCacheKey);
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function yearlySaleStore(array $data, int $ownerId): YearlySale
  {
    try {
      $existYearlySale = YearlySale::where('owner_id', $ownerId)->where('year', $data['year'])->first();

      if ($existYearlySale) {
        abort(400, 'その日の年次売上は既に存在しています！年次売上画面から編集をして数値を変更するか、削除してもう一度この画面から更新してください！');
      }

      $yearlySale = new YearlySale();
      $yearlySale->year = $data['year'];
      $yearlySale->yearly_sales = $data['yearly_sales'];
      $yearlySale->owner_id = $ownerId;
      $yearlySale->save();

      return $yearlySale;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function yearlySaleUpdate(array $data, int $yearlySaleId): YearlySale
  {
    try {
      $yearlySale = YearlySale::find($yearlySaleId);
      $yearlySale->year = $data['year'];
      $yearlySale->yearly_sales = $data['yearly_sales'];
      $yearlySale->save();

      return $yearlySale;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function yearlySaleValidateAndCreateOrUpdate(
    array $data,
    int $yearlySaleIdOrOwnerId,
    bool $createOrUpdate
  ): YearlySale {
    try {
      $validator = Validator::make($data, [
        'year' => 'required|string',
        'yearly_sales' => 'required|integer|min:0',
      ]);

      if ($validator->fails()) {
        throw new HttpException(403, '入力内容が正しくありません');
      }
      $validatedDate = $validator->validate();

      if ($createOrUpdate) {
        $ownerId = $yearlySaleIdOrOwnerId;
        return $this->yearlySaleStore($validatedDate, $ownerId);
      } else {
        $yearlySaleId = $yearlySaleIdOrOwnerId;
        return $this->yearlySaleUpdate($validatedDate, $yearlySaleId);
      }
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function yearlySaleDelete(int $yearlySaleId): void
  {
    try {
      $yearlySale = YearlySale::find($yearlySaleId);

      $yearlySale->delete();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }
}
