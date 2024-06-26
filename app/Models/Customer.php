<?php

namespace App\Models;

use App\Http\Controllers\OptionCustomersController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class  Customer extends Model
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
            Course::class,
            'course_customers'
        )->withPivot('owner_id');
    }

    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_customers')->withPivot('owner_id');
    }

    public function merchandises()
    {
        return $this->belongsToMany(Merchandise::class, 'merchandise_customers')->withPivot('owner_id');
    }

    public function hairstyles()
    {
        return $this->belongsToMany(Hairstyle::class, 'hairstyle_customers')->withPivot('owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'customer_users')->withPivot('owner_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'customer_id');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
