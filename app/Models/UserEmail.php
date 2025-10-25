<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ユーザーメールモデル
 *
 * @property string      $id              メールID(UUID)
 * @property string      $user_id         関連ユーザーID
 * @property string      $email           メールアドレス
 * @property bool        $is_primary      メインメールかどうか
 * @property bool        $is_pending      確認待ちかどうか ←★追加
 * @property string|null $verify_token    認証トークン（メール確認リンク用）←★追加
 * @property \DateTime|null $verified_at  認証済み日時
 * @property \DateTime|null $expires_at   トークン有効期限 ←★追加
 * @property \DateTime|null $created_at
 * @property \DateTime|null $updated_at
 */
class UserEmail extends BaseModel
{
    use HasFactory;

    protected $table = 'user_emails';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'email',
        'is_primary',
        'is_pending',     // ← 追加
        'verify_token',   // ← 追加
        'expires_at',     // ← 追加
        'verified_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_pending' => 'boolean',   // ← 追加
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',  // ← 追加
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** メールが紐づくユーザー */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** 認証済みかどうか */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /** トークンが有効かどうか */
    public function isTokenValid(string $token): bool
    {
        return $this->verify_token === $token && (!$this->expires_at || now()->lt($this->expires_at));
    }
}
