<?php

namespace App\Models;

use App\Models\course_customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class courses extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_name',
        'price',
    ];

    public function course_customers()
    {
        return $this->hasMany(course_customers::class);
    }

    public function customers()
    {
        return $this->belongsToMany(customers::class, 'course_customers');
    }

}
