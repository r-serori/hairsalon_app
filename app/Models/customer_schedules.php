<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_schedules extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'customers_id',
        'schedules_id',
    ];
}
