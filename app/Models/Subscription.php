<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * サブスクリプションモデル
 * 購読関係（フォロー）を表す。
 *
 * @property string      $id             サブスクリプションID
 * @property string      $subscriber_id  購読者のユーザーID
 * @property string      $subscribed_to_id 購読対象のユーザーID
 * @property \DateTime|null $created_at
 */
class Subscription extends BaseModel
{
    use HasFactory;

    protected $table = 'subscriptions';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'subscriber_id',
        'subscribed_to_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /** 購読者（フォロワー） */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    /** 購読対象（フォローされる側） */
    public function subscribedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subscribed_to_id');
    }
}
