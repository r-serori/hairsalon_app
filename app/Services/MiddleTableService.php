<?php


namespace App\Services;

use App\Models\CourseCustomer;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\HairstyleCustomer;
use App\Models\MerchandiseCustomer;
use App\Models\OptionCustomer;
use Illuminate\Support\Facades\Cache;
use \Illuminate\Database\Eloquent\Collection;


class MiddleTableService
{
  public function __construct()
  {
  }

  private function createCacheKey(int $ownerId, string $whoAreYou): string
  {
    return 'owner_' . $ownerId . $whoAreYou;
  }

  public  function rememberCache(int $ownerId, string $whoAreYou): Collection
  {
    try {
      $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）
      switch ($whoAreYou) {
        case 'course_customers':
          $cacheKey = $this->createCacheKey($ownerId, 'course_customers');
          return  Cache::remember($cacheKey, $expirationInSeconds, function () use ($ownerId) {
            return  CourseCustomer::where('owner_id', $ownerId)->get();
          });
          break;
        case 'option_customers':
          $cacheKey = $this->createCacheKey($ownerId, 'option_customers');
          return  Cache::remember($cacheKey, $expirationInSeconds, function () use ($ownerId) {
            return  OptionCustomer::where('owner_id', $ownerId)->get();
          });
          break;
        case 'merchandise_customers':
          $cacheKey = $this->createCacheKey($ownerId, 'merchandise_customers');
          return  Cache::remember($cacheKey, $expirationInSeconds, function () use ($ownerId) {
            return  MerchandiseCustomer::where('owner_id', $ownerId)->get();
          });
          break;
        case 'hairstyle_customers':
          $cacheKey = $this->createCacheKey($ownerId, 'hairstyle_customers');
          return  Cache::remember($cacheKey, $expirationInSeconds, function () use ($ownerId) {
            return  HairstyleCustomer::where('owner_id', $ownerId)->get();
          });
          break;
        default:
          abort(500, 'エラーが発生しました');
      }
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function flushCache(int $ownerId): void
  {
    try {
      $courseCustomersCacheKey = $this->createCacheKey($ownerId, 'course_customers');
      $optionCustomersCacheKey = $this->createCacheKey($ownerId, 'option_customers');
      $merchandiseCustomersCacheKey = $this->createCacheKey($ownerId, 'merchandise_customers');
      $hairstyleCustomersCacheKey = $this->createCacheKey($ownerId, 'hairstyle_customers');

      Cache::forget($courseCustomersCacheKey);
      Cache::forget($optionCustomersCacheKey);
      Cache::forget($merchandiseCustomersCacheKey);
      Cache::forget($hairstyleCustomersCacheKey);
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function pivotDataSync(
    int $ownerId,
    Customer $customer,
    array $data
  ): void {
    try {
      $courseIds = $data['course_id'] ?? [];
      $optionIds = $data['option_id'] ?? [];
      $merchandiseIds = $data['merchandise_id'] ?? [];
      $hairstyleIds = $data['hairstyle_id'] ?? [];
      $userIds = $data['user_id'];

      $pivotData = [];
      if (!empty($courseIds)) {
        foreach ($courseIds as $courseId) {
          $pivotData[$courseId] = ['owner_id' => $ownerId];
        }
      }

      // `course_id`がnullまたは空の場合、`sync`メソッドは空の配列を渡します。
      $customer->courses()->sync($pivotData);

      $pivotData = [];
      if (!empty($optionIds)) {
        foreach ($optionIds as $optionId) {
          $pivotData[$optionId] = ['owner_id' => $ownerId];
        }
      }

      $customer->options()->sync($pivotData);

      $pivotData = [];
      if (!empty($merchandiseIds)) {
        foreach ($merchandiseIds as $merchandiseId) {
          $pivotData[$merchandiseId] = ['owner_id' => $ownerId];
        }
      }

      $customer->merchandises()->sync($pivotData);

      $pivotData = [];
      if (!empty($hairstyleIds)) {
        foreach ($hairstyleIds as $hairstyleId) {
          $pivotData[$hairstyleId] = ['owner_id' => $ownerId];
        }
      }

      $customer->hairstyles()->sync($pivotData);


      $pivotData = [];
      foreach ($userIds as $userId) {
        $pivotData[$userId] = ['owner_id' => $ownerId];
      }
      $customer->users()->sync($pivotData);
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }
}
