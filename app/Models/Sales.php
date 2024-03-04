<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;
    protected $fillable = [
        'tax',
        'total_price',
        'created_at',
        //外部キーの追加
        'user_id',
        'course_id',
        'option_id',
        'merchandise_id',
        'customer_id',
        'schedule_id',
        
    ];
}
