<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchandise;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\MerchandiseService;
use Symfony\Component\HttpKernel\Exception\HttpException;


class MerchandisesController extends BaseController
{
    protected $getImportantIdService;
    protected $merchandiseService;
    protected $hasRole;

    public function __construct(GetImportantIdService $getImportantIdService, MerchandiseService $merchandiseService, HasRole $hasRole)
    {
        $this->getImportantIdService = $getImportantIdService;
        $this->merchandiseService = $merchandiseService;
        $this->hasRole = $hasRole;
    }

    public function index()
    {
        try {
            $user =  $this->hasRole->allAllow();


            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $merchandises = $this->merchandiseService->rememberCache($ownerId);

            if ($merchandises->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！新規作成ボタンから物販商品を作成しましょう！",
                    'merchandises' => []
                ]);
            } else {
                return $this->responseMan([
                    'merchandises' => $merchandises
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

            $merchandise = $this->merchandiseService->merchandiseValidateAndCreateOrUpdate($request->all(), $ownerId, true);

            $this->merchandiseService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                "merchandise" => $merchandise,
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

            $merchandise = $this->merchandiseService->merchandiseValidateAndCreateOrUpdate($request->all(), $request->id, false);

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $this->merchandiseService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                "merchandise" => $merchandise
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

            $this->merchandiseService->merchandiseDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->merchandiseService->forgetCache($ownerId);

            DB::commit();
            return $this->responseMan([
                "deleteId" => $request->id
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
