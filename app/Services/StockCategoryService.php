<?php

namespace App\Services;

use App\Models\StockCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StockCategoryService
{

  public function __construct()
  {
  }

  private function createCacheKey(int $ownerId): string
  {
    return 'owner_' . $ownerId . 'stockCategories';
  }

  public  function rememberCache(int $ownerId): Collection
  {
    try {
      $stockCategoriesCacheKey = $this->createCacheKey($ownerId);

      $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

      $stockCategories = Cache::remember($stockCategoriesCacheKey, $expirationInSeconds, function () use ($ownerId) {
        return StockCategory::where('owner_id', $ownerId)->orderBy('category', 'asc')->get();
      });

      return $stockCategories;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function forgetCache(int $ownerId): void
  {
    try {
      $stockCategoriesCacheKey = $this->createCacheKey($ownerId);

      Cache::forget($stockCategoriesCacheKey);
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function stockCategoryStore(array $data, int $ownerId): StockCategory
  {
    try {
      $stockCategory = new StockCategory();
      $stockCategory->category = $data['category'];
      $stockCategory->owner_id = $ownerId;
      $stockCategory->save();

      return $stockCategory;
    } catch (\Exception $e) {
      // Log::error($e->getMessage());

      abort(500, 'エラーが発生しました');
    }
  }

  private function stockCategoryUpdate(array $data, int $stockCategoryId): StockCategory
  {
    try {
      $stockCategory = StockCategory::find($stockCategoryId);
      $stockCategory->category = $data['category'];
      $stockCategory->save();

      return $stockCategory;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function stockCategoryValidateAndCreateOrUpdate(
    array $data,
    int $stockCategoryIdOrOwnerId,
    bool $createOrUpdate
  ): StockCategory {
    try {
      $validator = Validator::make($data, [
        'category' => 'required|string|max:100',
      ]);

      if ($validator->fails()) {
        throw new HttpException(403, '入力内容が正しくありません');
      }
      $validatedData = $validator->validate();

      if ($createOrUpdate) {
        $ownerId = $stockCategoryIdOrOwnerId;
        return $this->stockCategoryStore($validatedData, $ownerId);
      } else {
        $stockCategoryId = $stockCategoryIdOrOwnerId;
        return $this->stockCategoryUpdate($validatedData, $stockCategoryId);
      }
    } catch (\Exception $e) {
      // Log::error($e->getMessage());
      abort(500, 'エラーが発生しました');
    }
  }

  public  function stockCategoryDelete(int $stockCategoryId): void
  {
    try {
      $stockCategory = StockCategory::find($stockCategoryId);

      $stockCategory->delete();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }
}
