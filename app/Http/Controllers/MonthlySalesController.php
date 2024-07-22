<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\MonthlySaleService;
use Symfony\Component\HttpKernel\Exception\HttpException;


class MonthlySalesController extends BaseController
{
    protected $getImportantIdService;
    protected $monthlySaleService;
    protected $hasRole;

    public function __construct(GetImportantIdService $getImportantIdService, MonthlySaleService $monthlySaleService, HasRole $hasRole)
    {
        $this->getImportantIdService = $getImportantIdService;
        $this->monthlySaleService = $monthlySaleService;
        $this->hasRole = $hasRole;
    }

    public function index()
    {
        try {
            $user =  $this->hasRole->ownerAllow();

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $currentYear = Carbon::now()->year;

            $monthly_sale = $this->monthlySaleService->rememberCache($ownerId, $currentYear);

            if ($monthly_sale->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！予約表画面の月次売上更新ボタンから月次売上を作成しましょう！",
                    'monthlySales' => []
                ]);
            } else {
                return $this->responseMan([
                    'monthlySales' => $monthly_sale,
                    'message' => $currentYear . '年の月次売上データです！'
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

    public function selectedMonthlySales($year)
    {
        try {
            $user =  $this->hasRole->ownerAllow();

            $ownerId = Owner::where('user_id', $user->id)->value('id');
            $decodedYear = urldecode($year);

            $monthly_sale = $this->monthlySaleService->getMonthlySales($ownerId, $decodedYear);

            if ($monthly_sale->isEmpty()) {
                return $this->responseMan([
                    "message" => "選択した売上データがありません！予約表画面の月次売上更新ボタンから月次売上を作成しましょう！",
                    'monthlySales' => $monthly_sale
                ]);
            } else {
                return $this->responseMan([
                    'monthlySales' => $monthly_sale,
                    'message' => $decodedYear . '年の月次売上データです！'
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

            $monthly_sale = $this->monthlySaleService->monthlySaleValidateAndCreateOrUpdate($request->all(), $ownerId, true);

            $this->monthlySaleService->forgetCache($ownerId);

            DB::commit();
            return $this->responseMan([
                "monthlySale" => $monthly_sale,
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

            $monthly_sale = $this->monthlySaleService->monthlySaleValidateAndCreateOrUpdate($request->all(), $request->id, false);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->monthlySaleService->forgetCache($ownerId);

            DB::commit();
            // 成功したらリダイレクト
            return $this->responseMan([
                "monthlySale" => $monthly_sale,
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

            $this->monthlySaleService->monthlySaleDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->monthlySaleService->forgetCache($ownerId);

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
