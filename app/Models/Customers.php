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
        'owner_id',
    ];

    public function courses()
    {
        return $this->belongsToMany(
            courses::class,
            'course_customers'
        )->withPivot('owner_id');
    }

    public function options()
    {
        return $this->belongsToMany(options::class, 'option_customers')->withPivot('owner_id');
    }

    public function merchandises()
    {
        return $this->belongsToMany(merchandises::class, 'merchandise_customers')->withPivot('owner_id');
    }

    public function hairstyles()
    {
        return $this->belongsToMany(hairstyles::class, 'hairstyle_customers')->withPivot('owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'customer_users')->withPivot('owner_id');
    }

    public function schedules()
    {
        return $this->hasMany(schedules::class, 'customers_id');
    }

    public function owner()
    {
        return $this->belongsTo(owner::class, 'owner_id');
    }
}
