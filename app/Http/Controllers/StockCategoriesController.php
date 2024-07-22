<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockCategory;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\StockCategoryService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StockCategoriesController extends BaseController
{

    protected $getImportantIdService;
    protected $stockCategoryService;
    protected $hasRole;

    public function __construct(GetImportantIdService $getImportantIdService, StockCategoryService $stockCategoryService, HasRole $hasRole)
    {
        $this->getImportantIdService = $getImportantIdService;
        $this->stockCategoryService = $stockCategoryService;
        $this->hasRole = $hasRole;
    }

    public function index()
    {
        try {

            $user =  $this->hasRole->allAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $stockCategoriesCacheKey = 'owner_' . $ownerId . 'stockCategories';

            $expirationInSeconds = 60 * 60 * 24; // 1日（秒数で指定）
            // カテゴリ一覧を取得

            $stock_categories = Cache::remember($stockCategoriesCacheKey, $expirationInSeconds, function () use ($ownerId) {
                return StockCategory::where('owner_id', $ownerId)->get();
            });

            if ($stock_categories->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！新規作成ボタンから在庫カテゴリを作成しましょう！",
                    'stockCategories' => []
                ]);
            } else {
                return $this->responseMan([
                    'stockCategories' => $stock_categories
                ]);
            }
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->managerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            // 在庫モデルを作成して保存する
            $stock_category = $this->stockCategoryService->stockCategoryValidateAndCreateOrUpdate($request->all(), $ownerId, true);

            $this->stockCategoryService->forgetCache($ownerId);

            DB::commit();

            // 成功したらリダイレクト
            return $this->responseMan([
                "stockCategory" => $stock_category,
            ]);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->managerAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $stock_category = $this->stockCategoryService->stockCategoryValidateAndCreateOrUpdate($request->all(), $request->id, false);

            $this->stockCategoryService->forgetCache($ownerId);

            DB::commit();

            // 成功したらリダイレクト
            return $this->responseMan([
                "stockCategory" => $stock_category,
            ]);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }
    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();
            // 指定されたIDの在庫カテゴリを取得
            $this->stockCategoryService->stockCategoryDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->stockCategoryService->forgetCache($ownerId);

            DB::commit();
            return $this->responseMan([
                "deleteId" => $request->id,
            ]);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->serverErrorResponseWoman();
        }
    }
}
