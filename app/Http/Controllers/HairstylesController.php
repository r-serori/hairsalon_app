<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\HairstyleService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HairstylesController extends BaseController
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
            $user =  $this->hasRole->allAllow();

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

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

            $hairstyle = $this->hairstyleService->hairstyleValidateAndCreateOrUpdate($request->all(), $ownerId, true);

            $this->hairstyleService->forgetCache($ownerId);
            DB::commit();
            return $this->responseMan([
                "hairstyle" => $hairstyle,
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

            $hairstyle = $this->hairstyleService->hairstyleValidateAndCreateOrUpdate($request->all(), $request->id, false);

            $ownerId = $this->getImportantIdService->getOwnerId($user->id);

            $this->hairstyleService->forgetCache($ownerId);
            DB::commit();
            return $this->responseMan([
                "hairstyle" => $hairstyle,
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

            $this->hairstyleService->hairstyleDelete($request->id);

            $ownerId = Owner::where('user_id', $user->id)->value('id');

            $this->hairstyleService->forgetCache($ownerId);

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
