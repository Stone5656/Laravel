<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Pivots\VideoCategory;
use App\Models\Pivots\LiveStreamCategory;

/**
 * カテゴリモデル
 *
 * @property string      $id         カテゴリID(UUID)
 * @property string      $name       カテゴリ名
 * @property \DateTime|null $created_at
 * @property \DateTime|null $updated_at
 */
class Category extends BaseModel
{
    use HasFactory;

    protected $table = 'categories';
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

    /** このカテゴリに属する動画一覧 */
    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'video_categories', 'category_id', 'video_id')
            ->using(VideoCategory::class)
            ->withTimestamps()
            ->as('videoCategoryMeta');
    }

    /** このカテゴリに属するライブ配信一覧 */
    public function liveStreams(): BelongsToMany
    {
        return $this->belongsToMany(LiveStream::class, 'live_stream_categories', 'category_id', 'live_stream_id')
        ->using(LiveStreamCategory::class)
        ->withTimestamps()
        ->as('liveStreamCategoryMeta');;
    }

    /** VideoCategorysレコードへのアクセス */
    public function videoCategoryLinks()
    {
        return $this->hasMany(VideoCategory::class, 'category_id', 'id');
    }

    /** LiveStreamCategorysレコードへのアクセス */
    public function liveStreamCategoryLinks()
    {
        return $this->hasMany(LiveStreamCategory::class, 'category_id', 'id');
    }
}
