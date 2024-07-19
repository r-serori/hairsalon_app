<?php

namespace App\Services;

use App\Models\Merchandise;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;

class MerchandiseService
{

    public function __construct()
    {
    }
    public static function rememberCache(int $ownerId): Collection
    {
        $merchandisesCacheKey = 'owner_' . $ownerId . 'merchandises';

        $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

        $merchandises = Cache::remember($merchandisesCacheKey, $expirationInSeconds, function () use ($ownerId) {
            return  Merchandise::where('owner_id', $ownerId)->get();
        });

        return $merchandises;
    }

    public  function forgetCache(int $ownerId): void
    {
        $merchandisesCacheKey = 'owner_' . $ownerId . 'merchandises';

        Cache::forget($merchandisesCacheKey);
    }

    public  function MerchandiseValidate(array $data): array
    {
        $validator = Validator::make($data, [
            'merchandise_name' => 'required|string|max:100',
            'price' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            abort(400, '入力内容を確認してください！');
        }
        return $validator->validate();
    }

    public  function MerchandiseDelete(int $merchandiseId): void
    {
        $merchandise = Merchandise::find($merchandiseId);

        if (empty($merchandise)) {
            abort(404, '物販データが見つかりません');
        }

        $merchandise->delete();
    }
}
