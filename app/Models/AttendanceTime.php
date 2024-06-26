<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceTime extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'start_time',
        'end_time',
        'start_photo_path',
        'end_photo_path',
        'user_id',
    ];


    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
