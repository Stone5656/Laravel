<?php
// app/Models/LiveStream.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // ファクトリを使用する場合
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveStream extends Model
{
    use HasFactory; // ファクトリを使用する場合に追加

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',          // ER図に基づいて追加 (重要)
        'title',
        'description',
        // 'stream_key',    // 通常は自動生成またはシステム内部で設定。マスアサインメントで設定するかは要検討
        'status',
        'scheduled_at',     // ER図に基づいて追加
        'thumbnail_path',
        'archive_video_id', // ER図に基づいて追加 (nullableの想定)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'stream_key', // 配信キーは通常隠すべき情報
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * このライブ配信を所有するユーザーを取得します。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // /**
    //  * このライブ配信のアーカイブ動画を取得します (存在する場合)。
    //  */
    // public function archiveVideo(): BelongsTo
    // {
    //     // Videoモデルが存在すると仮定 (App\Models\Video)
    //     // 外部キー名を Eloquent の規約 (archive_video_id) と異なる名前にしている場合は、
    //     // 第2引数で外部キー名を指定する必要があります。
    //     // 第3引数で関連先の主キー名を指定することもできます。
    //     // return $this->belongsTo(Video::class, 'custom_fk_name', 'custom_owner_key_name');
    //     return $this->belongsTo(Video::class, 'archive_video_id');
    // }

    // 他のリレーションシップ (例: comments, chat_messages) もここに追加できます
}