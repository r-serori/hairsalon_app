<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchandise;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\MerchandiseService;


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
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->responseMan([
                "message" => "物販商品の取得に失敗しました！もう一度お試しください！"
            ], 500);
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
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "物販商品の作成に失敗しました！もう一度お試しください！"
            ], 500);
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
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "物販商品の更新に失敗しました！もう一度お試しください！"
            ], 500);
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
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "物販商品の削除に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }
}
