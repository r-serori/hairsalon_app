<?php

namespace App\Services;

use App\Models\Owner;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;

class OwnerService
{

  public function __construct()
  {
  }

  private function ownerPost(array $data, Owner $owner, int $user_id): Owner
  {
    try {
      $owner->store_name = $data['store_name'];
      $owner->postal_code = $data['postal_code'];
      $owner->prefecture = $data['prefecture'];
      $owner->city = $data['city'];
      $owner->addressLine1 = $data['addressLine1'];
      $owner->addressLine2 = $data['addressLine2'] ?? null;
      $owner->phone_number = $data['phone_number'];
      $owner->user_id = $user_id;
      $owner->save();

      return $owner;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function ownerStore(array $data, int $user_id): Owner
  {
    try {
      $owner = new Owner();
      $resOwner =  $this->ownerPost($data, $owner, $user_id);
      return $resOwner;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  private function ownerUpdate(array $data, int $user_id): Owner
  {
    try {
      $owner = Owner::where('user_id', $user_id)->first();
      $resOwner =  $this->ownerPost($data, $owner, $user_id);
      return $resOwner;
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public function ownerValidateAndCreateOrUpdate(array $data, int $user_id, bool $createOrUpdate): Owner
  // request->all()を受け取り、バリデーションを行い、createOrUpdateがtrueの場合はowner_idを受け取り、新規作成、falseの場合はowner_idを受け取り、更新を行う
  {
    try {
      $validator = Validator::make($data, [
        'store_name' => 'required|string|max:100',
        'postal_code' => 'required | string',
        'prefecture' => 'required | string | max:100',
        'city' => 'required | string | max:100',
        'addressLine1' => 'required | string | max:200',
        'addressLine2' => 'nullable | string | max:200',
        'phone_number' => 'required | string | max:20',
      ]);

      if ($validator->fails()) {
        abort(400, '入力内容を確認してください！');
      }

      $validatedData = $validator->validate();

      if ($createOrUpdate) {
        return $this->ownerStore($validatedData, $user_id);
      } else {
        return $this->ownerUpdate($validatedData, $user_id);
      }
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }

  public  function ownerDelete(int $ownerId): void
  {
    try {
      $owner = Owner::find($ownerId);

      if (empty($owner)) {
        abort(404, 'コースが見つかりません');
      }

      $owner->delete();
    } catch (\Exception $e) {
      abort(500, 'エラーが発生しました');
    }
  }
}
