<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hairstyle extends Model
{
    use HasFactory;
    protected $fillable = [
        'hairstyle_name',
        'created_at',
    ];

    public function customers()
    {
        return $this->hasMany(Customers::class);
    }

}
