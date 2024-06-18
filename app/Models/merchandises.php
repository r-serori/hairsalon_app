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
        'owner_id',
    ];



    public function customers()
    {
        return $this->belongsToMany(customers::class, 'merchandise_customers', 'merchandises_id', 'customers_id');
    }

    public function owner()
    {
        return $this->belongsTo(owner::class, 'owner_id');
    }
}
