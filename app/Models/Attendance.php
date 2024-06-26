<?php

namespace App\Models;

use App\Models\AttendanceTime;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'attendance_name',
        'position',
        'phone_number',
        'address',
        'isAttendance',
    ];
}
