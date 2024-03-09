<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name',
        'category',
        'quantity',
        'purchase_price',
        'supplier',
        'remarks',
        'created_at',
     
    ];
}
