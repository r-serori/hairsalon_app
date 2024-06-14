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

    public function users()
    {
        return $this->belongsToMany(User::class, 'customer_users', 'customers_id', 'users_id');
    }


    public function schedules()
    {
        return $this->hasMany(schedules::class, 'customers_id');
    }
}
