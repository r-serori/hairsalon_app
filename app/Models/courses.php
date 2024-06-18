<?php

namespace App\Models;

use App\Models\owner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class courses extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_name',
        'price',
        'owner_id'
    ];


    public function customers()
    {
        return $this->belongsToMany(customers::class, 'course_customers', 'courses_id', 'customers_id');
    }

    public function owner()
    {
        return $this->belongsTo(owner::class, 'owner_id');
    }
}
