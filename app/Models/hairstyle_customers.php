<?php

namespace App\Models;

use App\Models\hairstyles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hairstyle_customers extends Model
{
    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'id',
        'hairstyles_id',
        'customers_id',
    ];

    public function hairstyle()
    {
        return $this->belongsTo(hairstyles::class);
    }

    public function customer()
    {
        return $this->belongsTo(customers::class);
    }

    public function schedules()
    {
        return $this->hasMany(schedules::class);
    }
}
