<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamUser extends Model
{
  use HasFactory;

  protected $table = 'team_user';

  protected $fillable = [
    'team_id',
    'user_id',
    'role',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function team()
  {
    return $this->belongsTo(Team::class);
  }
}
