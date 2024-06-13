<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'address',
        'phone_number',
        'user_id',
    ];
}
