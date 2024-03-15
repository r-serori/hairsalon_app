<?php

namespace App\Models;
use App\Models\stocks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stock_categories extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'category',
        'created_at',
    ];

    
    public function stocks()
    {
        return $this->hasMany(stocks::class);
    }
}
