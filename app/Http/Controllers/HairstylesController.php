<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hairstyle;

use Illuminate\Support\Facades\Gate;
use App\Enums\Permissions;
use Illuminate\Http\JsonResponse;
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
use App\Services\HairstyleService;
use Illuminate\Support\Facades\Log;

class HairstylesController extends Controller
{
    protected $getImportantIdService;
    protected $hairstyleService;
    protected $hasRole;

    public function __construct(GetImportantIdService $getImportantIdService, HairstyleService $hairstyleService, HasRole $hasRole)
    {
        $this->getImportantIdService = $getImportantIdService;
        $this->hairstyleService = $hairstyleService;
        $this->hasRole = $hasRole;
    }

    public function index(): JsonResponse
    {
        try {

            $user =  $this->hasRole->AllAllow();

            $ownerId = $this->getImportantIdService->GetOwnerId($user->id);

            $hairstyles = $this->hairstyleService->rememberCache($ownerId);

            if ($hairstyles->isEmpty()) {
                return $this->responseMan([
                    "message" => "初めまして！新規作成ボタンからヘアスタイルを作成しましょう！",
                    'hairstyles' => []
                ]);
            } else {
                return $this->responseMan([
                    'hairstyles' => $hairstyles
                ]);
            }
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->responseMan([
                "message" => "ヘアスタイルの取得に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $user = $this->hasRole->ManagerAllow();

            $validatedData = $this->hairstyleService->HairstyleValidate($request->all());

            $ownerId = $this->getImportantIdService->GetOwnerId($user->id);

            $hairstyle = Hairstyle::create([
                'hairstyle_name' => $validatedData['hairstyle_name'],
                'owner_id' => $ownerId
            ]);

            $this->hairstyleService->forgetCache($ownerId);
            DB::commit();
            return $this->responseMan([
                "hairstyle" => $hairstyle,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "ヘアスタイルの作成に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }




    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ManagerAllow();

            $validatedData = $this->hairstyleService->HairstyleValidate($request->all());

            $hairstyle = Hairstyle::find($request->id);
            $hairstyle->hairstyle_name = $validatedData['hairstyle_name'];

            $hairstyle->save();
            $ownerId = $this->getImportantIdService->GetOwnerId($user->id);


            $this->hairstyleService->forgetCache($ownerId);
            DB::commit();
            return $this->responseMan([
                "hairstyle" => $hairstyle,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "ヘアスタイルの更新に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->OwnerAllow();

            $this->hairstyleService->HairstyleDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->hairstyleService->forgetCache($ownerId);

            DB::commit();
            return $this->responseMan([
                "deleteId" => $request->id,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "ヘアスタイルの削除に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }
}
