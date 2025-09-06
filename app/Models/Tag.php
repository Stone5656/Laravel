<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Pivots\VideoTag;
use App\Models\Pivots\LiveStreamTag;

/**
 * タグモデル
 *
 * @property string      $id         タグID(UUID)
 * @property string      $name       タグ名
 * @property \DateTime|null $created_at
 * @property \DateTime|null $updated_at
 */
class Tag extends BaseModel
{
    use HasFactory;

    protected $table = 'tags';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** このタグが付与されている動画一覧 */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'video_tags', 'tag_id', 'video_id')
        ->using(VideoTag::class)
        ->withTimestamps()
        ->as('videoTagMeta');
    }

    /** このタグが付与されているライブ配信一覧 */
    public function liveStreams(): BelongsToMany
    {
        return $this->belongsToMany(LiveStream::class, 'live_stream_tags', 'tag_id', 'live_stream_id')
        ->using(LiveStreamTag::class)
        ->withTimestamps()
        ->as('liveStreamTagMeta');
    }

    /** VideoTagsレコードへのアクセス */
    public function VideoTagLinks()
    {
        return $this->hasMany(VideoTag::class, 'tag_id', 'id');
    }

    /** LiveStreamTagsレコードへのアクセス */
    public function LiveStreamTagLinks()
    {
        return $this->hasMany(LiveStreamTag::class, 'tag_id', 'id');
    }
}
