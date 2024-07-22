<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\YearlySale;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\YearlySaleService;
use Illuminate\Support\Facades\Log;

class YearlySalesController extends BaseController
{
    protected $getImportantIdService;
    protected $yearlySaleService;
    protected $hasRole;

    public function __construct(GetImportantIdService $getImportantIdService, YearlySaleService $yearlySaleService, HasRole $hasRole)
    {
        $this->getImportantIdService = $getImportantIdService;
        $this->yearlySaleService = $yearlySaleService;
        $this->hasRole = $hasRole;
    }
    public function index()
    {

        try {

            $user =  $this->hasRole->ownerAllow();

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $yearly_sale = $this->yearlySaleService->rememberCache($ownerId);

            if ($yearly_sale->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！予約表画面の年次売上作成ボタンから年次売上を作成しましょう！",
                    'yearlySales' => []
                ]);
            } else {
                return $this->responseMan([
                    'yearlySales' => $yearly_sale,
                ]);
            }
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->responseMan([
                "message" => "年次売上の取得に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $yearly_sale = $this->yearlySaleService->yearlySaleValidateAndCreateOrUpdate($request->all(), $ownerId, true);

            $this->yearlySaleService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                "yearlySale" => $yearly_sale,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "年次売上の作成に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();

            $yearly_sale = $this->yearlySaleService->yearlySaleValidateAndCreateOrUpdate($request->all(), $request->id, false);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->yearlySaleService->forgetCache($ownerId);

            DB::commit();

            return $this->responseMan([
                "yearlySale" => $yearly_sale,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "年次売上の更新に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();
            $yearly_sale = $this->yearlySaleService->yearlySaleDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->yearlySaleService->forgetCache($ownerId);

            DB::commit();
            return $this->responseMan([
                "deleteId" => $request->id,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return  $this->responseMan([
                "message" => "年次売上の削除に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }
}
