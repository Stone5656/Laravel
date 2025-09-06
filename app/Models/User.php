<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Enums\UserStatusEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * ユーザーモデル
 *
 * @property string                 $id                     ユーザーID(UUID)
 * @property string                 $name                   表示名
 * @property string                 $email                  ログイン用メール
 * @property string|null            $password               パスワード（ハッシュ済）
 * @property string|null            $bio                    自己紹介
 * @property string|null            $channel_name           配信用チャンネル名
 * @property string|null            $profile_image_path     プロフィール画像パス
 * @property string|null            $cover_image_path       カバー画像パス
 * @property bool                   $is_stream              配信者フラグ
 * @property \RoleEnum::class       $roles                  ロール（例: user, moderator, admin）
 * @property \DateTime|null         $email_verified_at      メール確認日
 * @property string|null            $language               言語コード
 * @property string|null            $timezone               タイムゾーン
 * @property string|null            $phone_number           電話番号
 * @property \DateTime|null         $birthday               誕生日
 * @property \DateTime|null         $last_login_at          最終ログイン日時
 * @property int                    $login_failure_count    ログイン失敗回数
 * @property \UserStatusEnum::class $status                 ステータス (active/deleted/suspended)
 * @property string|null            $primary_email_id       メインメールのID (user_emails テーブル参照)
 * @property \DateTime|null         $created_at
 * @property \DateTime|null         $updated_at
 * @property \DateTime|null         $deleted_at
 */

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $table = 'users';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'bio',
        'channel_name',
        'profile_image_path',
        'cover_image_path',
        'is_stream',
        'roles',
        'email_verified_at',
        'language',
        'timezone',
        'phone_number',
        'birthday',
        'last_login_at',
        'login_failure_count',
        'status',
        'primary_email_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'roles'                => RoleEnum::class,
        'status'                => UserStatusEnum::class,
        'is_stream'            => 'boolean',
        'email_verified_at'    => 'datetime',
        'birthday'             => 'date',
        'last_login_at'        => 'datetime',
        'login_failure_count'  => 'integer',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
        'deleted_at'           => 'datetime',
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
    /** メールアドレス(複数可) */
    public function emails(): HasMany
    {
        return $this->hasMany(UserEmail::class);
    }

    /** このユーザーが所有する動画一覧 */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    /** このユーザーが所有するライブ配信一覧 */
    public function liveStreams(): HasMany
    {
        return $this->hasMany(LiveStream::class);
    }

    /** このユーザーが投稿したチャット一覧 */
    public function chatMessage(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /** このユーザーが購読しているユーザー一覧（followers） */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'subscriber_id');
    }

    /** このユーザーを購読しているユーザー一覧（followers） */
    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscription::class, 'subscribed_to_id');
    }
}
