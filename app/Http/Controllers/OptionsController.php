<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Option;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Owner;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\OptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class OptionsController extends BaseController
{
    protected $getImportantIdService;
    protected $optionService;
    protected $hasRole;

    public function __construct(GetImportantIdService $getImportantIdService, OptionService $optionService, HasRole $hasRole)
    {
        $this->getImportantIdService = $getImportantIdService;
        $this->optionService = $optionService;
        $this->hasRole = $hasRole;
    }

    public function index()
    {
        try {
            $user =  $this->hasRole->AllAllow();

            $ownerId = $this->getImportantIdService->GetOwnerId($user->id);

            $options = $this->optionService->rememberCache($ownerId);

            if ($options->isEmpty()) {
                return  $this->responseMan([
                    "message" => "初めまして！新規作成ボタンからオプションを作成しましょう！",
                    'options' => []
                ]);
            } else {
                return  $this->responseMan([
                    'options' => $options
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return  $this->responseMan([
                "message" => "オプションの取得に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ManagerAllow();

            $validatedData = $this->optionService->OptionValidate($request->all());

            $ownerId = $this->getImportantIdService->GetOwnerId($user->id);

            $option =
                Option::create([
                    'option_name' => $validatedData['option_name'],
                    'price' => $validatedData['price'],
                    'owner_id' => $ownerId
                ]);

            $this->optionService->forgetCache($ownerId);

            DB::commit();
            return  $this->responseMan([
                "option" => $option,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return  $this->responseMan([
                "message" => "オプションの作成に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $this->hasRole->ManagerAllow();

            $validatedData = $this->optionService->OptionValidate($request->all());

            $ownerId = $this->getImportantIdService->GetOwnerId($user->id);

            $option = Option::find($request->id);
            $option->option_name = $validatedData['option_name'];
            $option->price = $validatedData['price'];
            $option->save();

            $this->optionService->forgetCache($ownerId);

            DB::commit();
            return $this->responseMan([
                "option" => $option,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "オプションの更新に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->OwnerAllow();

            $this->optionService->OptionDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->optionService->forgetCache($ownerId);

            DB::commit();
            return $this->responseMan([
                "deleteId" => $request->id,
            ]);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            DB::rollBack();
            return $this->responseMan([
                "message" => "オプションの削除に失敗しました！もう一度お試しください！"
            ], 500);
        }
    }
}
