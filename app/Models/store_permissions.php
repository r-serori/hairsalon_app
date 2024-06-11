<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class store_permissions extends Model
{
    use HasFactory;

    protected $fillable = [
        'permission',
        'owner_id',
        'user_id',
    ];
}
