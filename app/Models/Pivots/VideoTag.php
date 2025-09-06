<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\BaseModel;
use App\Models\Video;
use App\Models\Tag;

/**
 * 動画とタグの中間モデル
 *
 * @property string $id       中間ID
 * @property string $video_id 動画ID
 * @property string $tag_id   タグID
 */
class VideoTag extends BaseModel
{
    use HasFactory;

    protected $table = 'video_tags';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'video_id',
        'tag_id',
    ];

    /** 親動画 */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /** 関連タグ */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
