<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hairstyle extends Model
{
    use HasFactory;
    protected $fillable = [
        'hairstyle_name',
        'owner_id',
    ];



    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'hairstyle_customers', 'hairstyle_id', 'customer_id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
