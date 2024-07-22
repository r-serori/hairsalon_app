<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CustomerService
{
  private $middleTableService;

  public function __construct(MiddleTableService $middleTableService)
  {
    $this->middleTableService = $middleTableService;
  }

  private function createCacheKey(int $ownerId): string
  {
    return 'owner_' . $ownerId . 'customers';
  }

  public function rememberCache(int $ownerId): Collection
  {
    try {
      $customersCacheKey =  $this->createCacheKey($ownerId);

      $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

      $customers = Cache::remember($customersCacheKey, $expirationInSeconds, function () use ($ownerId) {
        return  Customer::where('owner_id', $ownerId)->orderBy('customer_name', 'asc')->get();
      });

      return $customers;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function forgetCache(int $ownerId): void
  {
    try {
      $customersCacheKey = $this->createCacheKey($ownerId);

      Cache::forget($customersCacheKey);
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }


  private function customerStore(array $data, int $ownerId): Customer
  {
    try {
      $customer = new Customer();
      $customer->customer_name = $data['customer_name'];
      $customer->phone_number = $data['phone_number'];
      $customer->remarks = $data['remarks'];
      $customer->owner_id = $ownerId;
      $customer->save();

      return $customer;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function customerUpdate(array $data, int $customerId): Customer
  {
    try {
      $customer = Customer::find($customerId);
      $customer->customer_name = $data['customer_name'];
      $customer->phone_number = $data['phone_number'];
      $customer->remarks = $data['remarks'];
      $customer->save();

      return $customer;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function customerValidateAndCreateOrUpdate(
    array $data,
    int $ownerId,
    int|null $customerId,
    bool $createOrUpdate
  ): Customer
  // request->all()を受け取り、バリデーションを行い、createOrUpdateがtrueの場合はowner_idを受け取り、新規作成、falseの場合はcustomer_idを受け取り、更新を行う
  {
    try {
      $validator = Validator::make($data, [
        'customer_name' => 'required|string|max:100',
        'phone_number' => 'nullable|string|max:20',
        'remarks' => 'nullable|string|max:150',
        'course_id' => 'array|nullable',
        'course_id.*' => 'nullable|integer|exists:courses,id',
        'option_id' => 'nullable|array',
        'option_id.*' => 'nullable|integer|exists:options,id',
        'merchandise_id' => 'nullable|array',
        'merchandise_id.*' => 'nullable|integer|exists:merchandises,id',
        'hairstyle_id' => 'nullable|array',
        'hairstyle_id.*' => 'nullable|integer|exists:hairstyles,id',
        'user_id' => 'required|array',
        'user_id.*' => 'required|integer|exists:users,id',
      ]);

      if ($validator->fails()) {
        // Log::error($validator->errors());
        throw new HttpException(403, '入力内容が正しくありません');
      }

      $validatedData = $validator->validate();

      if ($createOrUpdate) {
        $customer = $this->customerStore($validatedData, $ownerId);
        $this->middleTableService->pivotDataSync($ownerId, $customer, $validatedData);
        return $customer;
      } else {
        $customer = $this->customerUpdate($validatedData, $customerId);
        $this->middleTableService->pivotDataSync($ownerId, $customer, $validatedData);
        return $customer;
      }
    } catch (\Exception $e) {
      // Log::error($e->getMessage());
      abort(500, 'エラーが発生しました');
    }
  }

  public  function customerDelete(int $customerId): void
  {
    try {
      $customer = Customer::find($customerId);

      $customer->delete();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }
}
