<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySale extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'daily_sales',
        'owner_id'
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
