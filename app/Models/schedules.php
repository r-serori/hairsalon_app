<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class schedules extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        "title",
        "start_time",
        "end_time",
        'allDay',
        'customers_id',
        'owner_id',
    ];


    public function customer()
    {
        return $this->belongsTo(customers::class);
    }

    public function owner()
    {
        return $this->belongsTo(owner::class, 'owner_id');
    }
}
