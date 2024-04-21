<?php

namespace App\Models;

use App\Models\merchandises;
use App\Models\customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class merchandise_customers extends Model
{
    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'id',
        'merchandise_id',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }

    public function merchandise()
    {
        return $this->belongsTo(merchandises::class);
    }

    public function schedules()
    {
        return $this->hasMany(schedules::class);
    }
}
