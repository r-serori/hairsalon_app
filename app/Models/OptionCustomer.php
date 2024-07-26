<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionCustomer extends Model
{

  public $timestamps = false;

  use HasFactory;
  protected $fillable = [
    'option_id',
    'customer_id',
    'owner_id'
  ];
}
