<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Providers\JetstreamServiceProvider;
use Laravel\Jetstream\Jetstream;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

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

    // ユーザーが登録された後に呼び出される処理
    protected static function booted()
    {
        static::created(function ($user) {
            // ユーザーのロールに基づいて権限を付与
            switch ($user->role) {
                case 'オーナー':
                    $user->syncRoles([Roles::OWNER]);
                    break;
                case 'マネージャー':
                    $user->syncRoles([Roles::MANAGER]);
                    break;
                case 'スタッフ':
                    $user->syncRoles([Roles::STAFF]);
                    break;
                default:
                    // デフォルトのロールや権限を設定する必要があればここに記述する
                    break;
            }
        });
    }





    public function attendance_times()
    {
        return $this->hasMany(attendance_times::class);
    }


    public function customers()
    {
        return $this->belongsToMany(customers::class, 'customer_attendances', 'attendances_id', 'customers_id');
    }

    public function ownedTeams()
    {
        return $this->teams()->where('user_id', $this->id)->orderBy('id', 'desc');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)->withPivot('role')->withTimestamps();
    }
}
