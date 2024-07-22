<?php

namespace App\Http\Controllers\Auth;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Controllers\BaseController;
use App\Models\Owner;
use App\Services\GetImportantIdService;
use App\Services\HasRole;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserGetController extends BaseController
{
    protected $hasRole;
    protected $getImportantIdService;

    public function __construct(HasRole $hasRole, GetImportantIdService $getImportantIdService)
    {
        $this->hasRole = $hasRole;
        $this->getImportantIdService = $getImportantIdService;
    }


    public function getUsers(): JsonResponse //userデータを取得
    {
        try {
            $user = $this->hasRole->allAllow();
            $response = $this->getImportantIdService->forGetUsersResponse($user);

            return $this->responseMan(
                $response
            );
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->serverErrorResponseWoman();
        }
    }

    public function show(): JsonResponse //単一のuserデータを取得
    {
        try {
            $user = $this->hasRole->allAllow();
            if (!empty($user)) {
                $responseUser = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'isAttendance' => $user->isAttendance,
                ];

                return $this->responseMan([
                    'message' => 'ユーザー情報を取得しました!',
                    'responseUser' => $responseUser,
                ]);
            } else {
                return $this->responseMan([
                    'message' => 'ユーザー情報がありません！',
                ], 404);
            }
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->serverErrorResponseWoman();
        }
    }

    public function getOwner(): JsonResponse //単一のオーナー情報を取得
    {
        try {
            $user = $this->hasRole->ownerAllow();

            $owner = Owner::where('user_id', $user->id)->first();

            return $this->responseMan([
                'message' => 'オーナー情報を取得しました!',
                'owner' => $owner,
            ]);
        } catch (HttpException $e) {
            // Log::error($e->getMessage());
            return $this->responseMan(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->serverErrorResponseWoman();
        }
    }
}
