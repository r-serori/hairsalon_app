<?php

namespace App\Models;
use App\Models\expenses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class expense_categories extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'category',
        'created_at',
    ];

    public function expenses()
    {
        return $this->hasMany(expenses::class);
    }

}
