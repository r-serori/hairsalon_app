<?php

namespace App\Enums;



class Roles
{
    public static $OWNER;
    public static $MANAGER;
    public static $STAFF;

    public static function initialize()
    {
        self::$OWNER = env("OWNER_ROLE", 'owner');
        self::$MANAGER = env("MANAGER_ROLE", 'manager');
        self::$STAFF = env("STAFF_ROLE", 'staff');
    }
}

//全員が触れるメソッドにはallPermissionをつける
//ownerPermissionはオーナーのみが触れるメソッドにつける
//managerPermissionはマネージャー以上が触れるメソッドにつける
class Permissions
{
    const ALL_PERMISSION = 'allPermission';
    const OWNER_PERMISSION = 'ownerPermission';
    const MANAGER_PERMISSION = 'managerPermission';
}
