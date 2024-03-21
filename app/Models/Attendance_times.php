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
        'attendance_id',
        'created_at',
        'updated_at',
    ];

    public function attendances()
    {
        return $this->belongsTo(attendances::class);
    }
}
