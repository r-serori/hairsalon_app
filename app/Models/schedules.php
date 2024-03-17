<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class schedules extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'customer_name',
        'date',
        'start_time',
        'end_time',
        'price',
        'customer_id',
        'created_at',
    ];



    public function customers()
    {
        return $this->belongsTo(customers::class);
    }

    public function course_customers()
    {
        return $this->hasMany(course_customers::class);
    }
    public function option_customers()
    {
        return $this->hasMany(option_customers::class);
    }
    public function merchandise_customers()
    {
        return $this->hasMany(merchandise_customers::class);
    }
    public function hairstyle_customers()
    {
        return $this->hasMany(hairstyle_customers::class);
    }



 
    public function courses()
    {
        return $this->belongsToMany(courses::class, 'course_schedules');
    }
    
    public function options()
    {
        return $this->belongsToMany(options::class, 'option_schedules');
    }

    public function merchandises()
    {
        return $this->belongsToMany(merchandises::class, 'merchandise_schedules');
    }
    
    public function hairstyles()
    {
        return $this->belongsToMany(hairstyles::class, 'hairstyle_schedules');
    }





}


