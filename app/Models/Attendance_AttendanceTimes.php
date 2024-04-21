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

    public function attendances()
    {
        return $this->belongsTo(attendances::class);
    }

    public function attendance_times()
    {
        return $this->belongsTo(attendance_times::class);
    }
}
