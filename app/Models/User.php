<?php
// app/Models/User.php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // メール認証を使用する場合
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Laravel Sanctumを使用する場合

class User extends Authenticatable // MustVerifyEmail を実装することも検討
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        // 追加したカラム
        'profile_image_path',
        'cover_image_path',
        'bio',
        'channel_name',
        'is_streamer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'roles' => \App\Enums\RoleEnum::class,
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'update_at' => 'datetime',
        'password' => 'hashed', // Laravel 10以降では自動的に処理されることが多い
        'is_streamer' => 'boolean', // 追加
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->id)) {
                $user->id = (string) \Illuminate\Support\Str::uuid();
            }

            // rolesが未定義なら自動設定（念のため）
            if (empty($user->roles)) {
                $user->roles = \App\Enums\RoleEnum::USER;
            }
        });
    }
}
