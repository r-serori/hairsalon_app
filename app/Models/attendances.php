<?php

namespace App\Models;

use App\Models\attendance_times;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class attendances extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'attendance_name',
        'position',
        'phone_number',
        'address',
        'created_at',
        'updated_at',
    ];

    public function attendance_times()
    {
        return $this->hasMany(attendance_times::class);
    }

    public function attendance_attendance_times()
    {
        return $this->hasMany(Attendance_AttendanceTimes::class);
    }

    public function customers()
    {
        return $this->belongsToMany(customers::class, 'customer_attendances', 'attendances_id', 'customers_id');
    }

    public function customer_attendance()
    {
        return $this->hasMany(customer_attendances::class);
    }
}
