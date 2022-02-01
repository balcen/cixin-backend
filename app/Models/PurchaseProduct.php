<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'name',
        'unit_price',
        'quantity',
        'unit',
        'total_price',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
