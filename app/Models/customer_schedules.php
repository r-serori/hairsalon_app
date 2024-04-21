<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_schedules extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'customers_id',
        'schedules_id',
    ];

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }

    public function schedule()
    {
        return $this->belongsTo(schedules::class);
    }
}
