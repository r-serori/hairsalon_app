<?php


namespace App\Services;

use App\Models\Option;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;

class OptionService
{
    public function __construct()
    {
    }
    public static function rememberCache(int $ownerId): Collection
    {
        $optionsCacheKey = 'owner_' . $ownerId . 'options';

        $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

        $options = Cache::remember($optionsCacheKey, $expirationInSeconds, function () use ($ownerId) {
            return  Option::where('owner_id', $ownerId)->get();
        });

        return $options;
    }

    public  function forgetCache(int $ownerId): void
    {
        $optionsCacheKey = 'owner_' . $ownerId . 'options';

        Cache::forget($optionsCacheKey);
    }

    public  function OptionValidate(array $data): array
    {
        $validator = Validator::make($data, [
            'option_name' => 'required|string|max:100',
            'price' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            abort(400, '入力内容を確認してください！');
        }
        return $validator->validate();
    }

    public  function OptionDelete(int $optionId): void
    {
        $option = Option::find($optionId);

        if (empty($option)) {
            abort(404, 'オプションデータが見つかりません');
        }

        $option->delete();
    }
}
