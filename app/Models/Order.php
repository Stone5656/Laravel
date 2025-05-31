<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'bookstore_id', 'employee_id', 'sum_price', 'order_detail', 'delibariy_date', 'order_date'
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'order_date' => 'datetime',
    ];

    public $timestamps = true;

    public function bookstore()
    {
        return $this->belongsTo(Bookstore::class);
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
