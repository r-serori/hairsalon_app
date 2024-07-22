<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\DailySaleService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DailySalesController extends BaseController
{

    protected $getImportantIdService;
    protected $dailySaleService;
    protected $hasRole;

    public function __construct(GetImportantIdService $getImportantIdService, DailySaleService $dailySaleService, HasRole $hasRole)
    {
        $this->getImportantIdService = $getImportantIdService;
        $this->dailySaleService = $dailySaleService;
        $this->hasRole = $hasRole;
    }

    public function index()
    {
        try {
            $user =  $this->hasRole->ownerAllow();

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $currentYear = Carbon::now()->year;

            $daily_sales = $this->dailySaleService->rememberCache($ownerId, $currentYear);

            if ($daily_sales->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！予約表画面の日次売上作成ボタンから日次売上を作成しましょう！",
                    'dailySales' => []
                ]);
            } else {
                return $this->responseMan([
                    'dailySales' => $daily_sales,
                    'message' => $currentYear . '年の日次売上データです！'
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
    public function selectedDailySales($year)
    {
        try {

            $user =  $this->hasRole->ownerAllow();

            $decodedYear = urldecode($year);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $daily_sales = $this->dailySaleService->getDailySales($ownerId, $decodedYear);

            if ($daily_sales->isEmpty()) {
                return $this->responseMan([
                    "message" => "選択した売上データがありません！予約表画面の日次売上作成ボタンから日次売上を作成しましょう！",
                    'dailySales' => $daily_sales
                ]);
            } else {
                return $this->responseMan([
                    'dailySales' => $daily_sales,
                    'message' => $decodedYear . '年の日次売上データです！'
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

            $user = $this->hasRole->ownerAllow();

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $daily_sale = $this->dailySaleService->dailySaleValidateAndCreateOrUpdate(
                $request->all(),
                $ownerId,
                true
            );

            $this->dailySaleService->forgetCache($ownerId);

            DB::commit();
            return $this->responseMan([
                "dailySale" => $daily_sale,
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
            $user = $this->hasRole->ownerAllow();

            $daily_sale = $this->dailySaleService->dailySaleValidateAndCreateOrUpdate(
                $request->all(),
                $request->id,
                false
            );

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->dailySaleService->forgetCache($ownerId);

            DB::commit();

            return
                $this->responseMan([
                    "dailySale" => $daily_sale
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

            $this->dailySaleService->dailySaleDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->dailySaleService->forgetCache($ownerId);

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
