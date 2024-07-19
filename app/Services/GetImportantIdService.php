<?php

namespace App\Services;

use App\Models\Owner;
use App\Models\Staff;

class GetImportantIdService
{
    public function __construct()
    {
    }

    public  function GetOwnerId($user_id): int
    {
        $staff = Staff::where('user_id', $user_id)->first();

        if (empty($staff)) {
            return  Owner::where('user_id', $user_id)->value('id');
        } else {
            return $staff->owner_id;
        }
    }
}
