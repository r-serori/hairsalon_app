<?php

namespace App\Models;

use App\Models\attendances;
use App\Models\customers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customer_attendances extends Model
{

    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'customers_id',
        'attendances_id',
    ];

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }

    public function attendance()
    {
        return $this->belongsTo(attendances::class);
    }


}
