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
        'created_at'
    ];


    public function option_customers()
    {
        return $this->hasMany(option_customers::class);
    }
    
    public function customers()
    {
        return $this->belongsToMany(customers::class, 'option_customers');
    }
}
