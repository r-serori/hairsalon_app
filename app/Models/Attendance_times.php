<?php

namespace App\Models;

use App\Models\attendances;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attendance_times extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'date',
        'start_time',
        'end_time',
        'break_time',
        'created_at',
        'updated_at',
    ];


    public function attendances()
    {
        return $this->belongsToMany(attendances::class, 'attendance_attendance_times', 'attendance_times_id', 'attendances_id');
    }
}
