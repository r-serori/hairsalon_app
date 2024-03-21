<?php

namespace App\Models;
use App\Models\expense_categories;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class expenses extends Model
{
    use HasFactory;
    protected $fillable = [
        'expense_name',
        'expense_price',
        'date',
        'expense_location',
        'remarks',
        'expense_category_id',
        'created_at',
    ];

    public function expense_category()
    {
        return $this->belongsTo(expense_categories::class);
    }

    public function expense_sales()
    {
        return $this->hasMany(expense_sales::class);
    }
}
