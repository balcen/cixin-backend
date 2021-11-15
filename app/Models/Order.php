<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'date',
        'name',
        'religion',
        'contact_person',
        'contact_tel',
        'status',
        'note'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
