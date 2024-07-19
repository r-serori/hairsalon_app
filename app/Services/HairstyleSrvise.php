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
    public static function rememberCache(int $ownerId): Collection
    {
        $hairstylesCacheKey = 'owner_' . $ownerId . 'hairstyles';

        $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

        $hairstyles = Cache::remember($hairstylesCacheKey, $expirationInSeconds, function () use ($ownerId) {
            return  Hairstyle::where('owner_id', $ownerId)->get();
        });

        return $hairstyles;
    }

    public  function forgetCache(int $ownerId): void
    {
        $hairstylesCacheKey = 'owner_' . $ownerId . 'hairstyles';

        Cache::forget($hairstylesCacheKey);
    }

    public  function HairstyleValidate(array $data): array
    {
        $validator = Validator::make($data, [
            'hairstyle_name' => 'required|string|max:100',

        ]);

        if ($validator->fails()) {
            abort(400, '入力内容を確認してください！');
        }
        return $validator->validate();
    }

    public  function HairstyleDelete(int $hairstyleId): void
    {
        $hairstyle = Hairstyle::find($hairstyleId);

        if (empty($hairstyle)) {
            abort(404, '髪型データが見つかりません');
        }

        $hairstyle->delete();
    }
}
