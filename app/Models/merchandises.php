<?php

namespace App\Models;

use App\Models\merchandise_customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class merchandises extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'merchandise_name',
        'price',
        'created_at'
    ];

    public function merchandise_customers()
    {
        return $this->hasMany(merchandise_customers::class);
    }


    public function customers()
    {
        return $this->belongsToMany(customers::class, 'merchandise_customers');
    }
}
