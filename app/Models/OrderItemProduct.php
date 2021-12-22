<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemProduct extends Model
{
    protected $fillable = [
        'order_item_id',
        'product_id',
        'name',
        'unit_price',
        'quantity',
        'unit',
        'total_price',
    ];
}
