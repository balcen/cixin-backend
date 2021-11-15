<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'work_item_id',
        'delivery_time',
        'deadline',
        'address',
        'vege_status',
        'note',
        'status',
    ];
}
