<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ← 追加

class Bookstore extends Model
{
    use SoftDeletes; // ← 論理削除を有効にする

    protected $table = 'bookstores';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'phone_number', 'post_code', 'address', 'discount_rate', 'deleted_at'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public $timestamps = true;
}
