<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'postal_code',
        'prefecture',
        'city',
        'addressLine1',
        'addressLine2',
        'phone_number',
        'user_id',
    ];
}
