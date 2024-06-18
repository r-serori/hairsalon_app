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
