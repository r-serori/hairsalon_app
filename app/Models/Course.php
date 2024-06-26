<?php

namespace App\Models;

use App\Models\Owner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_name',
        'price',
        'owner_id'
    ];


    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'course_customers', 'course_id', 'customer_id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
