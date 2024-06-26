<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCustomer extends Model
{

    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'course_id',
        'customer_id',
        'owner_id'
    ];
}
