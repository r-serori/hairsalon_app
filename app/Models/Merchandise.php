<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchandise extends Model
{
    use HasFactory;
    protected $fillable = [
        'merchandise_name',
        'price',
        'created_at',
    ];

    public function customers()
    {
        return $this->hasMany(Customers::class);
    }
}
