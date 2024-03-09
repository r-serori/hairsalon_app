<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'course_name',
        'price',
        'created_at',
    ];

    public function customers()
    {
        return $this->hasMany(Customers::class);
    }
    
}
