<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'phone_number',
        'features',
        'reservation_date',
        'reservation_start_time',
        'reservation_end_time',
        'new_customer',
        'created_at',
        //外部キーの追加
        'users_id',
        'courses_id',
    ];

    public function customers()
    {
        return $this->hasMany(Customers::class);
    }
}
