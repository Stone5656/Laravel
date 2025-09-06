<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\BaseModel;
use App\Models\Video;
use App\Models\Category;

/**
 * 動画とカテゴリの中間モデル
 *
 * @property string $id        中間ID
 * @property string $video_id  動画ID
 * @property string $category_id カテゴリID
 */
class VideoCategory extends BaseModel
{
    use HasFactory;

    protected $table = 'video_categories';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'video_id',
        'category_id',
    ];

    /** 親動画 */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /** 関連カテゴリ */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
