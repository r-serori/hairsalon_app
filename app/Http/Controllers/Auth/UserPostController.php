<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Owner;
use App\Models\Staff;
use App\Enums\Roles;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\HasRole;
use App\Services\GetImportantIdService;
use App\Services\OwnerService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserPostController extends BaseController
{
    protected $hasRole;
    protected $getImportantIdService;
    protected $ownerService;

    public function __construct(HasRole $hasRole, GetImportantIdService $getImportantIdService, OwnerService $ownerService)
    {
        $this->hasRole = $hasRole;
        $this->getImportantIdService = $getImportantIdService;
        $this->ownerService = $ownerService;
    }

    public function ownerStore(Request $request): JsonResponse //オーナーデータの登録
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();

            $owner = $this->ownerService->ownerValidateAndCreateOrUpdate($request->all(), $user->id, true);

            if (!empty($owner)) {
                DB::commit();
                return
                    $this->responseMan([
                        'message' => 'オーナー用ユーザー登録に成功しました!',
                        'owner' => $owner,
                    ]);
            } else {
                DB::rollBack();
                return $this->serverErrorResponseWoman();
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

    public function staffStore(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();

            $owner = Owner::where('user_id', $user->id)->first();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'email' => ' required|string|email|max:200|unique:users',
                'phone_number' => 'required|string|max:20|unique:users',
                'password' => 'required|string|max:100',
                'role' => 'required|string|max:30',
                'isAttendance' => 'required|boolean',
            ]);

            if ($validator->fails()) {
                return $this->responseMan([
                    'message' => '入力内容を確認してください！メールアドレスと電話番号が既に使用されている可能性があります！',
                ], 400);
            }

            $validateData = (object)$validator->validate();

            $user = User::create([
                'name' => $validateData->name,
                'email' => $validateData->email,
                'phone_number' => $validateData->phone_number,
                'password' => Hash::make($validateData->password),
                'role' => $validateData->role === 'マネージャー' ? Roles::$MANAGER : Roles::$STAFF,
                'isAttendance' => $validateData->isAttendance,
                'user_id' => $owner->id,
            ]);

            Staff::create([
                'user_id' => $user->id,
                'owner_id' => $owner->id,
            ]);

            $responseUser = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'role' => $user->role === Roles::$MANAGER ? 'マネージャー' : 'スタッフ',
                'isAttendance' => $user->isAttendance,
            ];

            DB::commit();
            return $this->responseMan([
                'message' => 'スタッフ用ユーザー登録に成功しました!',
                'responseUser' => $responseUser,
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

    public function ownerUpdate(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();

            $owner = $this->ownerService->ownerValidateAndCreateOrUpdate($request->all(), $user->id, false);

            DB::commit();

            return
                $this->responseMan([
                    'message' => 'オーナー情報の更新に成功しました!',
                    'owner' => $owner,
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

    public function updatePermission(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->hasRole->ownerAllow();
            $owner = Owner::where('user_id', $user->id)->first();

            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:users,id',
                'role' => 'required|string|max:30',
            ]);

            if ($validator->fails()) {
                return $this->responseMan([
                    'message' => '入力内容を確認してください！',
                ], 400);
            }

            $validateData = (object)$validator->validate();
            $updateUser = User::find($validateData->id);

            if (!empty($user)) {
                $updateUser->role = $validateData->role === 'マネージャー' ? Roles::$MANAGER : Roles::$STAFF;
                $updateUser->save();

                $responseUser = [
                    'id' => $updateUser->id,
                    'name' => $updateUser->name,
                    'phone_number' => $updateUser->phone_number,
                    'role' => $updateUser->role === Roles::$MANAGER ? 'マネージャー' : 'スタッフ',
                    'isAttendance' => $updateUser->isAttendance,
                ];

                DB::commit();

                return
                    $this->responseMan([
                        'message' => '権限の変更に成功しました！',
                        'responseUser' => $responseUser,
                    ]);
            } else {
                DB::rollBack();
                return $this->serverErrorResponseWoman();
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
}
