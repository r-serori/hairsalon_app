<?php

namespace App\Models;
use App\Models\customers;
use App\Models\options;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class option_customers extends Model
{

    public $timestamps = false;
    
    use HasFactory;
    protected $fillable = [
        'options_id',
        'customers_id',
    ];

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }
    
    public function option()
    {
        return $this->belongsTo(options::class);
    }

    public function schedules()
    {
        return $this->hasMany(schedules::class);
    }
}
