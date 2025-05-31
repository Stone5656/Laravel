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
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10以降では自動的に処理されることが多い
        'is_streamer' => 'boolean', // 追加
    ];
}