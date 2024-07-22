<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\OptionService;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
            $user =  $this->hasRole->allAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

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

            $option = $this->optionService->optionValidateAndCreateOrUpdate(
                $request->all(),
                $ownerId,
                true
            );

            $this->optionService->forgetCache($ownerId);

            DB::commit();
            return  $this->responseMan([
                "option" => $option,
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

            $option = $this->optionService->optionValidateAndCreateOrUpdate(
                $request->all(),
                $request->id,
                false
            );

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $this->optionService->forgetCache($ownerId);

            DB::commit();
            return $this->responseMan([
                "option" => $option,
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

            $this->optionService->optionDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->optionService->forgetCache($ownerId);

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
