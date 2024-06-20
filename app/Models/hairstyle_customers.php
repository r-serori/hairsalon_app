<?php

namespace App\Models;

use App\Models\hairstyles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hairstyle_customers extends Model
{
    public $timestamps = false;

    use HasFactory;
    protected $fillable = [
        'id',
        'hairstyles_id',
        'customers_id',
        'owner_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // シーダー実行中の場合はスキップ
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }

            $staff = staff::where('user_id', auth()->user()->id)->first();

            if (empty($staff)) {
                $owner = owner::where('user_id', auth()->user()->id)->first();
                $model->owner_id = $owner->id;
            } else {
                $owner = owner::where('staff_id', $staff->id)->first();
                $model->owner_id = $owner->owner_id;
            }
        });
    }
}
