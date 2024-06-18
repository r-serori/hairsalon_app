<?php

namespace App\Models;

use App\Models\hairstyle_customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hairstyles extends Model
{
    use HasFactory;
    protected $fillable = [

        'hairstyle_name',
        'owner_id',
    ];



    public function customers()
    {
        return $this->belongsToMany(customers::class, 'hairstyle_customers', 'hairstyles_id', 'customers_id');
    }

    public function owner()
    {
        return $this->belongsTo(owner::class, 'owner_id');
    }
}
