<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class yearly_sales extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'year',
        'year_sales',
        'created_at',
    ];

    
}
