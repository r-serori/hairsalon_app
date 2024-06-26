<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySale extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'year_month',
        'monthly_sales',
        'owner_id',
    ];


    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
