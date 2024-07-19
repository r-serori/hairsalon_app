<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchandise;
use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\MerchandiseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


class MerchandisesController extends Controller
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
            $user =  $this->hasRole->AllAllow();


            $ownerId = $this->getImportantIdService->GetOwnerId($user->id);

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
            $user = $this->hasRole->ManagerAllow();

            $validatedData = $this->merchandiseService->MerchandiseValidate($request->all());

            $ownerId = $this->getImportantIdService->GetOwnerId($user->id);

            $merchandise = Merchandise::create([
                'merchandise_name' => $validatedData['merchandise_name'],
                'price' => $validatedData['price'],
                'owner_id' => $ownerId

            ]);

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

            $user = $this->hasRole->ManagerAllow();

            $validatedData = $this->merchandiseService->MerchandiseValidate($request->all());

            $merchandise = Merchandise::find($request->id);
            $merchandise->merchandise_name = $validatedData['merchandise_name'];
            $merchandise->price = $validatedData['price'];
            $merchandise->save();

            $ownerId = $this->getImportantIdService->GetOwnerId($user->id);

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

            $user = $this->hasRole->OwnerAllow();

            $this->merchandiseService->MerchandiseDelete($request->id);

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
