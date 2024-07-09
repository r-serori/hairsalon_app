<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
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
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    // 型キャストの設定
    protected $casts = [
        'isAttendance' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];




    public function attendance_times()
    {
        return $this->hasMany(AttendanceTime::class);
    }


    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_users', 'user_id', 'customer_id');
    }

    public function ownedTeams()
    {
        return $this->teams()->where('user_id', $this->id)->orderBy('id', 'desc');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)->withPivot('role')->withTimestamps();
    }


    public function getKey()
    {
        return $this->id; // 例えば、プライマリーキーが 'id' の場合
    }

    public function getEmailForVerification()
    {
        return $this->email; // ユーザーのメールアドレスを返す例
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
