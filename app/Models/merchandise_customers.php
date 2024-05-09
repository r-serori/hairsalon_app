<?php

namespace App\Models;

use App\Models\merchandises;
use App\Models\customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class merchandise_customers extends Model
{
    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'merchandises_id',
        'customers_id',
    ];
}
