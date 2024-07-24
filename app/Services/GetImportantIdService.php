<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Owner;
use App\Models\Staff;
use App\Models\User;
use App\Enums\Roles;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;


class GetImportantIdService
{
    public function __construct()
    {
    }

    public  function getOwnerId(int $user_id): int // ユーザーIDからオーナーIDを取得
    {
        try {

            $staff = Staff::where('user_id', $user_id)->first();

            if (empty($staff)) {
                return  Owner::where('user_id', $user_id)->value('id');
            } else {
                return $staff->owner_id;
            }
        } catch (HttpException $e) {
            abort(500, 'エラーが発生しました。');
        }
    }

    public function getResponseUser(int $ownerId): array|Collection // オーナーIDからユーザー情報を取得
    {
        try {
            $staffs = Staff::where('owner_id', $ownerId)->pluck('user_id');
            // Log::info('staff', $staff->toArray());

            if ($staffs->isEmpty()) {
                $owner = Owner::find($ownerId);
                $owUser = User::find($owner->user_id);
                $responseUsers =
                    [
                        'id' => $owUser->id, 'name' => $owUser->name,
                        'isAttendance' =>  $owUser->isAttendance
                    ];
            } else {
                $owner = Owner::find($ownerId);
                $OwnersUser = User::find($owner->user_id);
                $userIds =  $staffs->push($OwnersUser->id);
                $users = User::whereIn('id', $userIds)->get();
                $responseUsers =
                    $users->map(function ($user) {
                        return [
                            'id' => $user->id, 'name' => $user->name,
                            'isAttendance' =>  $user->isAttendance
                        ];
                    });
            }
            return $responseUsers;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました。');
        }
    }

    public function forGetUsersResponse(User $user): array|Collection // ユーザー情報を取得
    {
        try {
            $user->role === Roles::$OWNER ?
                $ownerId = $this->getOwnerId($user->id) : $ownerId = $this->getOwnerId($user->id);

            $staffs = Staff::where('owner_id', $ownerId)->get();

            if ($staffs->isEmpty()) {
                $responseUsers = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone_number' => $user->phone_number,
                    'role' => $user->role === Roles::$OWNER ? 'オーナー' : ($user->role === Roles::$MANAGER ? 'マネージャー' : 'スタッフ'),
                    'isAttendance' => $user->isAttendance,
                ];

                return [
                    'message' => 'スタッフ情報がありません!登録してください！',
                    'responseUsers' => $responseUsers,
                ];
            } else {

                $userIds = $staffs->pluck('user_id');

                if ($user->role === Roles::$OWNER) {
                    $userIds->push($user->id);
                }

                $users = User::whereIn('id', $userIds)->get();

                $responseUsers = $users->map(function ($resUser) {
                    return [
                        'id' => $resUser->id,
                        'name' => $resUser->name,
                        'phone_number' => $resUser->phone_number,
                        'role' => $resUser->role === Roles::$OWNER ? 'オーナー' : ($resUser->role === Roles::$MANAGER ? 'マネージャー' : 'スタッフ'),
                        'isAttendance' => $resUser->isAttendance,
                    ];
                });

                $userCount = count($users);

                return [
                    'message' => 'ユーザー情報を取得しました!',
                    'responseUsers' => $responseUsers,
                    'userCount' => $userCount,
                ];
            }
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました。');
        }
    }
}
