<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone_number',
        'features',
        'created_at',
        'user_id',
        'course_id',
        'option_id',
        'merchandise_id',
        'hairstyle_id',
        'schedule_id',

    ];
}
