<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class schedules extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        "title",
        "start_time",
        "end_time",
        'allDay',
        'created_at',
        'updated_at',
    ];

    // キャストする日付フィールドを指定
    // protected $casts = [
    //     'start_time' => 'datetime',
    //     'end_time' => 'datetime',
    // ];

    public function customer()
    {
        return $this->belongsToMany(customers::class, 'customer_schedules', 'schedules_id', 'customers_id');
    }
}
