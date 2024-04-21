<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class schedules extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'date',
        'start_time',
        'end_time',
        'price',
        'created_at',
        'updated_at',
    ];



    public function customer_schedules()
    {
        return $this->hasMany(customer_schedules::class);
    }
}
