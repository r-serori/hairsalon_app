<?php

namespace App\Services;

use App\Models\Merchandise;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MerchandiseService
{

    public function __construct()
    {
    }

    private function createCacheKey(int $ownerId): string
    {
        return 'owner_' . $ownerId . 'merchandises';
    }

    public  function rememberCache(int $ownerId): Collection
    {
        try {
            $merchandisesCacheKey = $this->createCacheKey($ownerId);

            $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

            $merchandises = Cache::remember($merchandisesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                return  Merchandise::where('owner_id', $ownerId)->orderBy('merchandise_name', 'asc')->get();
            });

            return $merchandises;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public  function forgetCache(int $ownerId): void
    {
        try {
            $merchandisesCacheKey = $this->createCacheKey($ownerId);

            Cache::forget($merchandisesCacheKey);
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    private function MerchandiseStore(array $data, int $ownerId): Merchandise
    {
        try {
            $merchandise = new Merchandise();
            $merchandise->merchandise_name = $data['merchandise_name'];
            $merchandise->price = $data['price'];
            $merchandise->owner_id = $ownerId;
            $merchandise->save();

            return $merchandise;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    private function MerchandiseUpdate(array $data, int $merchandiseId): Merchandise
    {
        try {
            $merchandise = Merchandise::find($merchandiseId);
            $merchandise->merchandise_name = $data['merchandise_name'];
            $merchandise->price = $data['price'];
            $merchandise->save();

            return $merchandise;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public  function MerchandiseValidateAndCreateOrUpdate(
        array $data,
        int $merchandiseIdOrOwnerId,
        bool $createOrUpdate
    ): Merchandise {
        try {
            $validator = Validator::make($data, [
                'merchandise_name' => 'required|string|max:100',
                'price' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                throw new HttpException(403, '入力内容が正しくありません');
            }
            $validatedData = $validator->validate();
            if ($createOrUpdate) {
                $ownerId = $merchandiseIdOrOwnerId;
                return $this->MerchandiseStore($validatedData, $ownerId);
            } else {
                $merchandiseId = $merchandiseIdOrOwnerId;
                return $this->MerchandiseUpdate($validatedData, $merchandiseId);
            }
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public  function MerchandiseDelete(int $merchandiseId): void
    {
        try {
            $merchandise = Merchandise::find($merchandiseId);

            $merchandise->delete();
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }
}
