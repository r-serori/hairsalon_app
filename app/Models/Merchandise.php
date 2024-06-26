<?php

namespace App\Models;

use App\Models\merchandise_customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchandise extends Model
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
        return $this->belongsToMany(Customer::class, 'merchandise_customers', 'merchandise_id', 'customer_id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
