<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamInvitation extends Model
{
  use HasFactory;

  protected $fillable = [
    'team_id',
    'email',
    'role',
    'token',
  ];

  public function team()
  {
    return $this->belongsTo(Team::class);
  }
}
