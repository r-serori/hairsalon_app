<?php

namespace App\Models;
use App\Models\hairstyle_customers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hairstyles extends Model
{
    use HasFactory;
    protected $fillable = [
        'hairstyle_name',
    ];

    public function hairstyle_customers()
    {
        return $this->hasMany(hairstyle_customers::class);
    }

    public function customers()
    {
        return $this->belongsToMany(customers::class, 'hairstyle_customers');
}
}