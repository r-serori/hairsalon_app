<?php

namespace App\Models;

use App\Models\customers;
use App\Models\courses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class course_customers extends Model
{

    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'courses_id',
        'customers_id',
        'owner_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $staff = staff::where('user_id', auth()->user()->id)->first();

            if (empty($staff)) {
                $owner = owner::where('user_id', auth()->user()->id)->first();
                $model->owner_id = $owner->id;
            } else {
                $owner = owner::where('staff_id', $staff->id)->first();
                $model->owner_id = $owner->owner_id;
            }




            staff::where('user_id', auth()->user()->id)->first();
        });
    }
}
