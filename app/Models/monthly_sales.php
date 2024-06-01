<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class monthly_sales extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'year_month',
        'monthly_sales',
        'created_at'
    ];
}
