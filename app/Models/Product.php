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

    protected $appends = [
        'unit_name',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function getUnitNameAttribute()
    {
        return $this->unit->name;
    }

    public function getProductCategoryTrackingNumberAttribute()
    {
        return $this->productCategory->tracking_number;
    }
}
