<?php

namespace App\Http\Controllers\Auth;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Controllers\BaseController;
use App\Models\Owner;
use App\Services\GetImportantIdService;
use App\Services\HasRole;

class UserGetController extends BaseController
{
    protected $hasRole;
    protected $getImportantIdService;

    public function __construct(HasRole $hasRole, GetImportantIdService $getImportantIdService)
    {
        $this->hasRole = $hasRole;
        $this->getImportantIdService = $getImportantIdService;
    }

    public function getUsers(): JsonResponse
    {
        try {
            $user = $this->hasRole->allAllow();
            $response = $this->getImportantIdService->forGetUsersResponse($user);

            return $this->responseMan(
                $response
            );
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return $this->responseMan([
                'message' => 'エラーが発生しました。もう一度やり直してください！',
            ], 500);
        }
    }

    public function show(): JsonResponse
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
        } catch (\Exception $e) {
            return $this->responseMan([
                'message' => 'エラーが発生しました。もう一度やり直してください！',
            ], 500);
        }
    }


    public function getOwner(): JsonResponse
    {
        try {
            $user = $this->hasRole->ownerAllow();

            $owner = Owner::where('user_id', $user->id)->first();

            return $this->responseMan([
                'message' => 'オーナー情報を取得しました!',
                'owner' => $owner,
            ]);
        } catch (\Exception $e) {
            return $this->responseMan([
                'message' => 'エラーが発生しました。もう一度やり直してください！',
            ], 500);
        }
    }
}
