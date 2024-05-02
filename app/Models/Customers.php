<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class customers extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'customer_name',
        'phone_number',
        'remarks',
        'new_customer',
        'created_at',
        'updated_at',
    ];

    public function courses()
    {
        return $this->belongsToMany(courses::class, 'course_customers');
    }

    public function options()
    {
        return $this->belongsToMany(options::class, 'option_customers');
    }

    public function merchandises()
    {
        return $this->belongsToMany(merchandises::class, 'merchandise_customers');
    }

    public function hairstyles()
    {
        return $this->belongsToMany(hairstyles::class, 'hairstyle_customers');
    }

    public function attendances()
    {
        return $this->belongsToMany(attendances::class, 'customer_attendances', 'customers_id', 'attendances_id');
    }

    public function customer_attendances()
    {
        return $this->hasMany(customer_attendances::class);
    }

    public function schedules()
    {
        return $this->hasMany(schedules::class);
    }
}
