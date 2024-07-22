<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\StockService;

class StocksController extends BaseController
{
    protected $getImportantIdService;
    protected $stockService;
    protected $hasRole;

    public function __construct(GetImportantIdService $getImportantIdService, StockService $stockService, HasRole $hasRole)
    {
        $this->getImportantIdService = $getImportantIdService;
        $this->stockService = $stockService;
        $this->hasRole = $hasRole;
    }

    public function index(): JsonResponse
    {
        try {
            $user = $this->hasRole->allAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $stocks = $this->stockService->rememberCache($ownerId);

            if ($stocks->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！新規作成ボタンから店の在庫を作成しましょう！",
                    'stocks' => []
                ]);
            } else {
                return $this->responseMan([
                    'stocks' => $stocks
                ]);
            }
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->responseMan([
                "message" => "在庫の取得に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->managerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $stock = $this->stockService->stockValidateAndCreateOrUpdate($request->all(), $ownerId, true);

            $this->stockService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                "stock" => $stock,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "在庫の登録に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->managerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $stock = $this->stockService->stockValidateAndCreateOrUpdate($request->all(), $request->id, false);

            $stocksCacheKey = 'owner_' . $ownerId . 'stocks';

            Cache::forget($stocksCacheKey);

            DB::commit();

            return $this->responseMan([
                "stock" => $stock,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "在庫の更新に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();
            $this->stockService->stockDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->stockService->forgetCache($ownerId);

            DB::commit();
            return $this->responseMan([
                "deleteId"  => $request->id,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->responseMan([
                'message' => '在庫の削除に失敗しました！もう一度お試しください！'
            ], 500);
        }
    }
}
