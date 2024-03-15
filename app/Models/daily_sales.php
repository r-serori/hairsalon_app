<?php

namespace App\Models;
use App\Models\schedules;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class daily_sales extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'daily_sales'
    ];

    public function schedules()
    {
        return $this->hasMany(schedules::class);
    }
}
