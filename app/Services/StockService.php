<?php

namespace App\Services;

use App\Models\Stock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StockService
{

  public function __construct()
  {
  }

  private function createCacheKey(int $ownerId): string
  {
    return 'owner_' . $ownerId . 'stocks';
  }

  public  function rememberCache(int $ownerId): Collection
  {
    try {
      $stocksCacheKey = $this->createCacheKey($ownerId);

      $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

      $stocks = Cache::remember($stocksCacheKey, $expirationInSeconds, function () use ($ownerId) {
        return Stock::where('owner_id', $ownerId)->orderBy('product_name', 'asc')->get();
      });

      return $stocks;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function forgetCache(int $ownerId): void
  {
    try {
      $stocksCacheKey = $this->createCacheKey($ownerId);

      Cache::forget($stocksCacheKey);
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function stockStore(array $data, int $ownerId): Stock
  {
    try {
      $stock = new Stock();
      $stock->product_name = $data['product_name'];
      $stock->quantity = $data['quantity'];
      $stock->product_price = $data['product_price'];
      $stock->supplier = $data['supplier'];
      $stock->remarks = $data['remarks'];
      $stock->notice = $data['notice'];
      $stock->stock_category_id = $data['stock_category_id'];
      $stock->owner_id = $ownerId;
      $stock->save();

      return $stock;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function stockUpdate(array $data, int $stockId): Stock
  {
    try {
      $stock = Stock::find($stockId);
      $stock->product_name = $data['product_name'];
      $stock->quantity = $data['quantity'];
      $stock->product_price = $data['product_price'];
      $stock->supplier = $data['supplier'];
      $stock->remarks = $data['remarks'];
      $stock->notice = $data['notice'];
      $stock->stock_category_id = $data['stock_category_id'];
      $stock->save();

      return $stock;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function stockValidateAndCreateOrUpdate(
    array $data,
    int $stockIdOrOwnerId,
    bool $createOrUpdate
  ): Stock {
    try {
      $validator = Validator::make($data, [
        'product_name' => 'required|string|max:100',
        'quantity' => 'required|integer|min:0',
        'product_price' => 'required|integer|min:0',
        'supplier' => 'nullable|string|max:100',
        'remarks' => 'nullable|string|max:150',
        "notice" => "required|integer|min:0",
        'stock_category_id' => 'nullable|exists:stock_categories,id',
      ]);

      if ($validator->fails()) {
        throw new HttpException(403, '入力内容が正しくありません');
      }
      $validatedData = $validator->validate();

      if ($createOrUpdate) {
        $$ownerId = $stockIdOrOwnerId;
        return $this->stockStore($validatedData, $ownerId);
      } else {
        $stockId = $stockIdOrOwnerId;
        return $this->stockUpdate($validatedData, $stockId);
      }
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function stockDelete(int $stockId): void
  {
    try {
      $stock = Stock::find($stockId);

      $stock->delete();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }
}
