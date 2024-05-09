<?php

namespace App\Models;

use App\Models\customers;
use App\Models\courses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class course_customers extends Model
{

    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'courses_id',
        'customers_id',
    ];
}
