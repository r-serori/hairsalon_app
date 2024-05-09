<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance_AttendanceTimes extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'attendances_id',
        'attendance_times_id',
    ];
}
