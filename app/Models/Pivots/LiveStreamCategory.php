<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\BaseModel;
use App\Models\LiveStream;
use App\Models\Category;

/**
 * ライブ配信とカテゴリの中間モデル
 *
 * @property string $id             中間ID
 * @property string $live_stream_id ライブID
 * @property string $category_id    カテゴリID
 */
class LiveStreamCategory extends BaseModel
{
    use HasFactory;

    protected $table = 'live_stream_categories';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'live_stream_id',
        'category_id',
    ];

    /** このレコードが属するライブ */
    public function liveStream(): BelongsTo
    {
        return $this->belongsTo(LiveStream::class);
    }

    /** このレコードが指すカテゴリ */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
