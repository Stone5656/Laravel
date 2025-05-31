<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';

    protected $primaryKey = null; // 複合主キーを使用するのでnullに設定
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'order_id', 'productbook_id', 'order_price', 'order_stock'
    ];

    public $timestamps = true;

    /**
     * 注文に対するリレーション（多対一）
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * 商品書籍に対するリレーション（多対一）
     */
    public function productBook()
    {
        return $this->belongsTo(ProductBook::class, 'productbook_id');
    }
}
