<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class option_schedules extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'options_id',
        'schedules_id',
    ];

    public function schedules()
    {
        return $this->belongsTo(schedules::class);
    }
    
}
