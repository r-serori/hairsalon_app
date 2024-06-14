<?php

namespace App\Models;

use App\Models\attendance_times;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attendances extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'attendance_name',
        'position',
        'phone_number',
        'address',
        'isAttendance',
        'created_at',
        'updated_at',
    ];
}
