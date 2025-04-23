<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $incrementing = false; // ✅ UUID bukan auto-increment
    protected $keyType = 'string';

    protected $fillable = [
        'first_name', 'last_name', 'phone_number', 'address', 'pin', 'balance'
    ];

    protected $hidden = [
        'pin',
        'remember_token',
    ];
    

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid(); // ✅ generate UUID
            }
        });
    }
}
