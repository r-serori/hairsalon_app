<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "email",
        'phone_number',
        "password",
        "role",
        "isAttendance"
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 型キャストの設定
    protected $casts = [
        'isAttendance' => 'boolean',
    ];



    public function attendance_times()
    {
        return $this->hasMany(attendance_times::class);
    }


    public function customers()
    {
        return $this->belongsToMany(customers::class, 'customer_users', 'users_id', 'customers_id');
    }

    public function ownedTeams()
    {
        return $this->teams()->where('user_id', $this->id)->orderBy('id', 'desc');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)->withPivot('role')->withTimestamps();
    }



    /**
     * Check if the user has the specified role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}
