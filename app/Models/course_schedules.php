<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class course_schedules extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'id',
        'courses_id',
        'schedules_id',
    ];

    public function schedules()
    {
        return $this->belongsTo(schedules::class);
    }
}
