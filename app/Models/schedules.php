<?php

namespace App\Models;
use App\Models\customer_prices;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class schedules extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'date',
        'start_time',
        'end_time',
        'price',
        'customer_id',
        'created_at',
    ];



    public function customers()
    {
        return $this->belongsTo(customers::class);
    }
}
