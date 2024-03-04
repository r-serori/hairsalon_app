<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'position',
        'start_time',
        'end_time',
        'break_time',
        'address',
        'created_at',
        //外部キーの追加
        'users_id',
    ];

}
