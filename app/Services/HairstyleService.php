<?php

namespace App\Services;

use App\Models\Hairstyle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;

class HairstyleService
{

    public function __construct()
    {
    }
    private function createCacheKey(int $ownerId): string
    {
        return 'owner_' . $ownerId . 'hairstyles';
    }

    public  function rememberCache(int $ownerId): Collection
    {
        try {
            $hairstylesCacheKey = $this->createCacheKey($ownerId);

            $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

            $hairstyles = Cache::remember($hairstylesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                return  Hairstyle::where('owner_id', $ownerId)->get();
            });

            return $hairstyles;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public function forgetCache(int $ownerId): void
    {
        try {
            $hairstylesCacheKey = $this->createCacheKey($ownerId);

            Cache::forget($hairstylesCacheKey);
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    private function hairstyleStore(array $data, int $ownerId): Hairstyle
    {
        try {
            $hairstyle = new Hairstyle();
            $hairstyle->hairstyle_name = $data['hairstyle_name'];
            $hairstyle->owner_id = $ownerId;
            $hairstyle->save();

            return $hairstyle;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    private function hairstyleUpdate(array $data, int $hairstyleId): Hairstyle
    {
        try {
            $hairstyle = Hairstyle::find($hairstyleId);
            $hairstyle->hairstyle_name = $data['hairstyle_name'];
            $hairstyle->save();

            return $hairstyle;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public function hairstyleValidateAndCreateOrUpdate(
        array $data,
        int $hairstyleIdOrOwnerId,
        bool $createOrUpdate
    ): Hairstyle {
        try {
            $validator = Validator::make($data, [
                'hairstyle_name' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                abort(400, '入力内容を確認してください！');
            }
            $validatedData = $validator->validate();

            if ($createOrUpdate) {
                $ownerId = $hairstyleIdOrOwnerId;
                return $this->hairstyleStore($validatedData, $ownerId);
            } else {
                $hairstyleId = $hairstyleIdOrOwnerId;
                return $this->hairstyleUpdate($validatedData, $hairstyleId);
            }
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public  function hairstyleDelete(int $hairstyleId): void
    {
        try {
            $hairstyle = Hairstyle::find($hairstyleId);

            if (empty($hairstyle)) {
                abort(404, '髪型データが見つかりません');
            }

            $hairstyle->delete();
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }
}
