<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hairstyle;
use App\Models\Course;
use App\Models\Option;
use App\Models\Merchandise;


class Customers extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone_number',
        'features',
        'created_at',
        'user_id',
        'course_id',
        'option_id',
        'merchandise_id',
        'hairstyle_id',
        'schedule_id',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hairstyle()
    {
        return $this->belongsTo(Hairstyle::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function merchandise()
    {
        return $this->belongsTo(Merchandise::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
