<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = [
        "title",
        "start_time",
        "end_time",
        'allDay',
        'customer_id',
        'owner_id',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
