<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\BaseModel;
use App\Models\LiveStream;
use App\Models\Tag;

/**
 * ライブ配信とタグの中間モデル
 *
 * @property string $id             中間ID
 * @property string $live_stream_id ライブID
 * @property string $tag_id         タグID
 */
class LiveStreamTag extends BaseModel
{
    use HasFactory;

    protected $table = 'live_stream_tags';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'live_stream_id',
        'tag_id',
    ];

    /** ライブ配信 */
    public function liveStream(): BelongsTo
    {
        return $this->belongsTo(LiveStream::class);
    }

    /** タグ */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
