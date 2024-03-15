<?php

namespace App\Models;
use App\Models\stock_categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stocks extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'product_name',
        'quantity',
        'remarks',
        'supplier',
        'stock_category_id',
        'created_at'
    ];              
    
    public function stock_categories()
    {
        return $this->belongsTo(stock_categories::class);
    }

}
