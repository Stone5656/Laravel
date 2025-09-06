<?php

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * App\Models\User
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $profile_image_path
 * @property string|null $cover_image_path
 * @property string|null $bio
 * @property string|null $channel_name
 * @property bool $is_stream
 * @property RoleEnum $roles
 * @property string|null $pending_email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $language
 * @property string|null $time_zone
 * @property string|null $phone_number
 * @property \Illuminate\Support\Carbon|null $birth_day
 */

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'profile_image_path',
        'cover_image_path',
        'bio',
        'channel_name',
        'is_stream',
        'language',
        'time_zone',
        'phone_number',
        'birth_day',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'roles' => RoleEnum::class,
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'birth_day' => 'date',
        'password' => 'hashed',
        'is_stream' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->id)) {
                $uuid = (string) Str::uuid();
                if (!Str::isUuid($uuid)) {
                    throw ValidationException::withMessages(['id' => 'Invalid UUID']);
                }
                $user->id = $uuid;
            }

            if (empty($user->roles)) {
                $user->roles = RoleEnum::USER;
            }
        });
    }

    /**
     * JWTの「主体（sub）」に入れる識別子を返す
     * 通常はEloquentの主キー（UUIDやID）でOK
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // $this->id
    }

    /**
     * 追加のカスタムクレーム（任意）を返す
     * 何も足さない場合は空配列
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'roles'       => $this->roles?->value, // Enumを使っているため ->value
            'is_stream' => (bool) $this->is_stream,
            'email_verified' => (bool) $this->email_verified_at, // !is_null($this->email_verified_at)でも同じ意味だがbool値であることが分かりにくいためお勧めしない
        ];
    }
}
