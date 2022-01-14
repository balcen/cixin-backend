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

    protected $appends = [
        'product_tracking_number',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function getProductTrackingNumberAttribute()
    {
        return $this->product ? $this->prduct->tracking_number : '';
    }
}
