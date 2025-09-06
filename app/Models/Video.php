<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Pivots\VideoTag;
use App\Models\Pivots\VideoCategory;

/**
 * 動画モデル
 *
 * @property string      $id             動画ID
 * @property string      $user_id        投稿者ユーザーID
 * @property string      $title          タイトル
 * @property string|null $description    説明文
 * @property string      $file_path      動画ファイルパス
 * @property string|null $thumbnail_path サムネイル画像パス
 * @property int|null    $duration       再生時間(秒)
 * @property int|null    $views          再生回数
 * @property \DateTime|null $created_at
 * @property \DateTime|null $updated_at
 */
class Video extends BaseModel
{
    use HasFactory;

    protected $table = 'videos';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'description',
        'file_path',
        'thumbnail_path',
        'duration',
        'views',
    ];

    protected $casts = [
        'duration'   => 'integer',
        'views'      => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** 投稿者（ユーザー） */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** この動画のタグ一覧 */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'video_tags', 'video_id', 'tag_id')
        ->using(VideoTag::class)
        ->withTimestamps()
        ->as('videoTagMeta');
    }

    /** この動画のカテゴリ一覧 */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'video_categories', 'video_id', 'category_id')
        ->using(VideoCategory::class)
        ->withTimestamps()
        ->as('videoCategoryMeta');
    }


    /** VideoTagsレコードへのアクセス */
    public function VideoTagLinks()
    {
        return $this->hasMany(VideoTag::class, 'video_id', 'id');
    }

    /** VideoCategorysレコードへのアクセス */
    public function VideoCategoryLinks()
    {
        return $this->hasMany(VideoCategory::class, 'video_id', 'id');
    }
}
