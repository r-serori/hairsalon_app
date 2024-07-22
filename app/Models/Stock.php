<?php

namespace App\Models;

use App\Models\stock_categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name',
        'product_price',
        'quantity',
        'remarks',
        'supplier',
        'notice',
        'stock_category_id',
        'owner_id'
    ];

    public function stock_category()
    {
        return $this->belongsTo(StockCategory::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
