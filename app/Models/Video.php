<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
// use Illuminate\Database\Eloquent\Relations\MorphMany; // コメントやいいね機能で後々使用

class Video extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'video_path',       // 動画ファイルのパス
        'thumbnail_path',   // サムネイル画像のパス
        'duration',         // 動画の長さ（秒など）
        'visibility',       // 公開設定 (public, private, unlistedなど)
        'status',           // 動画の状態 (pending, processing, published, failedなど)
        'published_at',     // 公開日時
        // 'views_count' は通常、直接マスアサインメントせず、プログラムでインクリメントするため、$fillable には含めないことが多い
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'duration' => 'integer',
        'views_count' => 'integer',
    ];

    /**
     * この動画をアップロードしたユーザーを取得します。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * もしこの動画がライブ配信のアーカイブである場合、そのライブ配信情報を取得します。
     * LiveStream モデルの archive_video_id がこのVideoのidを参照します。
     */
    public function liveStreamArchive(): HasOne
    {
        return $this->hasOne(LiveStream::class, 'archive_video_id', 'id');
    }

    /*
    // --- 将来追加するリレーションシップの例 ---

    // 動画へのコメントを取得 (ポリモーフィック)
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // 動画への「いいね」を取得 (ポリモーフィック)
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // 動画が属するカテゴリを取得 (ポリモーフィック多対多)
    // public function categories(): MorphToMany
    // {
    //     return $this->morphToMany(Category::class, 'categorizable');
    // }

    // 動画に付けられたタグを取得 (ポリモーフィック多対多)
    // public function tags(): MorphToMany
    // {
    //     return $this->morphToMany(Tag::class, 'taggable');
    // }
    */
}