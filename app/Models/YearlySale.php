<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlySale extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'year',
        'yearly_sales',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
