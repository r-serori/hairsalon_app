<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Attendance_times extends Model
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
        //外部キーの追加
        'attendance_id',
    ];
    public function attendance()
    {
        return $this->belongsTo('App\Models\Attendance');
    }

}
