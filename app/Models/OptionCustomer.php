<?php

namespace App\Models;

use App\Models\customers;
use App\Models\options;

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

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         // シーダー実行中の場合はスキップ
    //         if (app()->runningInConsole() && !app()->runningUnitTests()) {
    //             return;
    //         }

    //         $staff = Staff::where('user_id', auth()->user()->id)->first();

    //         if (empty($staff)) {
    //             $owner = owner::where('user_id', auth()->user()->id)->first();
    //             $model->owner_id = $owner->id;
    //         } else {
    //             $owner = owner::where('staff_id', $staff->id)->first();
    //             $model->owner_id = $owner->owner_id;
    //         }
    //     });
    // }
}
