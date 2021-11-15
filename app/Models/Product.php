<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'tracking_number',
        'product_category_id',
        'name',
        'unit_id',
        'price',
        'safety_stock',
        'spec',
        'is_comb',
        'note',
    ];
}
