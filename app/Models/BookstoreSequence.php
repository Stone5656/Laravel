<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookstoreSequence extends Model
{
    // テーブル名がモデル名の複数形であれば指定不要
    protected $table = 'bookstore_sequences';

    // 主キーが'id'かつ整数型で自動インクリメントしないなら設定する
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';

    // ホワイトリスト方式（代入可能な属性の定義）
    protected $fillable = ['id'];

    // タイムスタンプ使用（created_at, updated_at）
    public $timestamps = true;
}
