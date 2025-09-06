<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Pivots\LiveStreamTag;
use App\Models\Pivots\LiveStreamCategory;

/**
 * ライブ配信モデル
 *
 * @property string      $id           ライブID
 * @property string      $user_id      配信者ユーザーID
 * @property string      $title        タイトル
 * @property string|null $description  説明文
 * @property \DateTime|null $scheduled_at 配信予定日時
 * @property \DateTime|null $started_at   配信開始日時
 * @property \DateTime|null $ended_at     配信終了日時
 * @property string      $status       ステータス(例: scheduled, live, finished)
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

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'description',
        'scheduled_at',
        'started_at',
        'ended_at',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

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
    public function LiveStreamTagLinks()
    {
        return $this->hasMany(LiveStreamTag::class, 'live_stream_id', 'id');
    }

    /** LiveStreamCategorysレコードへのアクセス */
    public function liveStreamCategoryLinks()
    {
        return $this->hasMany(LiveStreamCategory::class, 'live_stream_id', 'id');
    }
}
