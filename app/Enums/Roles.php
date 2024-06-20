<?php

namespace App\Enums;

class Roles
{
  const OWNER = 'オーナー';
  const MANAGER = 'マネージャー';
  const STAFF = 'スタッフ';
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
