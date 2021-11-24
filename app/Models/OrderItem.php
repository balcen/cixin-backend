<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function workItem()
    {
        return $this->belongsTo(WorkItem::class);
    }

    public function getCustomerAbbrAttribute()
    {
        return $this->order
            ->customer
            ->abbreviation;
    }

    public function getItemNameAttribute()
    {
        return $this->workItem
            ->name;
    }
}
