<?php

namespace App\Models;

use Carbon\Carbon;
use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

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

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::creating(function ($model) {
            $model->tracking_number = Carbon::now()->format('mdy') . Base::numerify('####');
        });
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCustomerAbbrAttribute()
    {
        return $this->customer->abbreviation;
    }
}
