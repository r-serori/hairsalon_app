<?php

namespace App\Services;

use App\Models\Option;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OptionService
{
    public function __construct()
    {
    }

    private function createCacheKey(int $ownerId): string
    {
        return 'owner_' . $ownerId . 'options';
    }

    public function rememberCache(int $ownerId): Collection
    {
        try {
            $optionsCacheKey = $this->createCacheKey($ownerId);

            $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）

            $options = Cache::remember($optionsCacheKey, $expirationInSeconds, function () use ($ownerId) {
                return  Option::where('owner_id', $ownerId)->orderBy('option_name', 'asc')->get();
            });

            return $options;
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            abort(500, 'エラーが発生しました');
        }
    }

    public function forgetCache(int $ownerId): void
    {
        try {
            $optionsCacheKey = $this->createCacheKey($ownerId);

            Cache::forget($optionsCacheKey);
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    private function OptionStore(array $data, int $ownerId): Option
    {
        try {
            $option = new Option();
            $option->option_name = $data['option_name'];
            $option->price = $data['price'];
            $option->owner_id = $ownerId;
            $option->save();

            return $option;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    private function optionUpdate(array $data, int $optionId): Option
    {
        try {
            $option = Option::find($optionId);
            $option->option_name = $data['option_name'];
            $option->price = $data['price'];
            $option->save();

            return $option;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public function optionValidateAndCreateOrUpdate(
        array $data,
        int $ownerIdOrOptionId,
        bool $createOrUpdate
    ): Option {
        try {
            $validator = Validator::make($data, [
                'option_name' => 'required|string|max:100',
                'price' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                throw new HttpException(403, '入力内容が正しくありません');
            }
            $validatedData = $validator->validate();

            if ($createOrUpdate) {
                $ownerId = $ownerIdOrOptionId;
                return $this->optionStore($validatedData, $ownerId);
            } else {
                $optionId = $ownerIdOrOptionId;
                return $this->optionUpdate($validatedData, $optionId);
            }
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }

    public  function optionDelete(int $optionId): void
    {
        try {
            $option = Option::find($optionId);

            $option->delete();
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました');
        }
    }
}
