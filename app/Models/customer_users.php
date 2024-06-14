<?php

namespace App\Models;

use App\Models\attendances;
use App\Models\customers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_users extends Model
{

    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'customers_id',
        'users_id',
    ];
}
