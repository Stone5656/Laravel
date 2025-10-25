<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

use App\Models\Pivots\LiveStreamTag;
use App\Models\Pivots\LiveStreamCategory;

/**
 * ライブ配信モデル
 *
 * @property string         $id
 * @property string         $user_id
 * @property string         $title
 * @property string|null    $description
 * @property \DateTime|null $scheduled_at
 * @property \DateTime|null $started_at
 * @property \DateTime|null $ended_at
 * @property string         $status         ステータス(例: scheduled, live, finished)
 * @property string         $stream_key     配信用キー（機微情報）
 * @property \DateTime|null $created_at
 * @property \DateTime|null $updated_at
 */
class LiveStream extends BaseModel
{
    use HasFactory;

    protected $table = 'live_streams';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /** JSON へは出さない（APIで返す場合は Resource 側で制御） */
    protected $hidden = ['stream_key']; // Eloquent のシリアライズ制御。:contentReference[oaicite:1]{index=1}

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'description',
        'scheduled_at',
        'started_at',
        'ended_at',
        'status',
        'stream_key', // 手動設定する場合も想定して許容
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ]; // キャスト仕様。:contentReference[oaicite:2]{index=2}

    /**
     * stream_key を未設定時に自動生成
     * boot/creating イベントでの初期化は一般的な手法。:contentReference[oaicite:3]{index=3}
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->stream_key)) {
                // 32 文字のランダムキー（必要に応じて長さ/方式は調整）
                $model->stream_key = Str::random(32);
            }
        });
    }

    /** 配信者（ユーザー） */
    public function streamer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** このライブに付属するチャットメッセージ一覧 */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /** このライブのタグ一覧 */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'live_stream_tags', 'live_stream_id', 'tag_id')
            ->using(LiveStreamTag::class)
            ->withTimestamps()
            ->as('liveStreamTagMeta');
    }

    /** このライブのカテゴリー一覧 */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'live_stream_categories', 'live_stream_id', 'category_id')
            ->using(LiveStreamCategory::class)
            ->withTimestamps()
            ->as('liveStreamCategoryMeta');
    }

    /** LiveStreamTagsレコードへのアクセス */
    public function liveStreamTagLinks()
    {
        return $this->hasMany(LiveStreamTag::class, 'live_stream_id', 'id');
    }

    /** LiveStreamCategorysレコードへのアクセス */
    public function liveStreamCategoryLinks()
    {
        return $this->hasMany(LiveStreamCategory::class, 'live_stream_id', 'id');
    }
}
