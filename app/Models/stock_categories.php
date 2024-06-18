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
        'owner_id',
    ];


    public function stocks()
    {
        return $this->hasMany(stocks::class);
    }

    public function owner()
    {
        return $this->belongsTo(owner::class, 'owner_id');
    }
}
