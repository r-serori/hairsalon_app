<?php

namespace App\Models;

use App\Models\stocks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'category',
        'owner_id',
    ];


    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
