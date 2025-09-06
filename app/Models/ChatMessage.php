<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * チャットメッセージモデル
 *
 * @property string      $id             メッセージID
 * @property string      $user_id        投稿者ユーザーID
 * @property string      $live_stream_id ライブ配信ID
 * @property string      $message        メッセージ本文
 * @property \DateTime|null $created_at
 * @property \DateTime|null $updated_at
 */
class ChatMessage extends BaseModel
{
    use HasFactory;

    protected $table = 'chat_messages';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'live_stream_id',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** 投稿者ユーザー */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** このメッセージが属するライブ配信 */
    public function liveStream(): BelongsTo
    {
        return $this->belongsTo(LiveStream::class);
    }
}
