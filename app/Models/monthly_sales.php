<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class monthly_sales extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'year',
        'month',
        'month_sales',
        'created_at'
    ];
}
