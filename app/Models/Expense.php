<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = [
        'expense_name',
        'price',
        'remarks',
        'expense_date',
        'expense_category',
        'tax',
        'expense_location',
        'total_amount',
        'created_at',
        //外部キーの追加
        'stock_id',

    ];
}
