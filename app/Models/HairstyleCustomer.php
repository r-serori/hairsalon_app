<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HairstyleCustomer extends Model
{

  public $timestamps = false;

  use HasFactory;
  protected $fillable = [
    'hairstyle_id',
    'customer_id',
    'owner_id'
  ];
}
