<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'position',
        'phone_number',
        'address',
        'created_at',
    ];
    public function attendance_times()
    {
        return $this->hasMany('App\Models\Attendance_times');
    }

}
