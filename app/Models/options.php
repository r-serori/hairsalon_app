<?php

namespace App\Models;

use App\Models\option_customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class options extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'option_name',
        'price',
        'owner_id'
    ];


    public function customers()
    {
        return $this->belongsToMany(customers::class, 'option_customers');
    }

    public function owner()
    {
        return $this->belongsTo(owner::class, 'owner_id');
    }
}
