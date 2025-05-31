<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBook extends Model
{
    protected $table = 'product_books';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'price', 'stock'
    ];

    public $timestamps = true;
}
