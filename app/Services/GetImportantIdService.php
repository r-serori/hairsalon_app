<?php

namespace App\Services;

use App\Models\Owner;
use App\Models\Staff;
use App\Models\User;
use App\Enums\Roles;
use \Illuminate\Database\Eloquent\Collection;

class GetImportantIdService
{
    public function __construct()
    {
    }

    public  function getOwnerId(int $user_id): int
    {
        try {
            $staff = Staff::where('user_id', $user_id)->first();

            if (empty($staff)) {
                return  Owner::where('user_id', $user_id)->value('id');
            } else {
                return $staff->owner_id;
            }
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました。');
        }
    }

    public function getResponseUser(int $ownerId): mixed
    {
        try {
            $staffs = Staff::where('owner_id', $ownerId)->pluck('user_id');
            // Log::info('staff', $staff->toArray());

            if ($staffs->isEmpty()) {
                $owner = Owner::find($ownerId);
                $owUser = User::find($owner->user_id);
                $responseUsers =
                    ['id' => $owUser->id, 'name' => $owUser->name];
            } else {
                $owner = Owner::find($ownerId);
                $OwnersUser = User::find($owner->user_id);
                $staffs->push($OwnersUser->id);
                $users = User::whereIn('id', $staffs)->get();
                $responseUsers =
                    $users->map(function ($user) {
                        return ['id' => $user->id, 'name' => $user->name];
                    });
            }
            return $responseUsers;
        } catch (\Exception $e) {
            abort(500, 'エラーが発生しました。');
        }
    }

    public function forGetUsersResponse(User $user): array
    {

        $owner = Owner::where('user_id', $user->id)->first();

        $staffs = Staff::where('owner_id', $owner->id)->get();

        if ($staffs->isEmpty()) {
            $responseUsers = $user->only([
                'id',
                'name',
                'phone_number',
                'role' => $user->role === Roles::$OWNER ? 'オーナー' : 'マネージャー',
                'isAttendance',
            ]);
            return [
                'message' => 'スタッフ情報がありません!登録してください！',
                'responseUsers' => $responseUsers,
            ];
        } else {

            $userIds = $staffs->pluck('user_id');

            $userIds->push($user->id);

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
    }
}
