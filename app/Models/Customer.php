<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'tracking_number',
        'name',
        'abbreviation',
        'principal',
        'contact_person',
        'tax_number',
        'invoice_address',
        'company_address',
        'company_tel_1',
        'company_tel_2',
        'company_tel_3',
        'company_fax',
        'company_email',
        'company_url',
        'online_order_number',
        'online_order_password',
        'payment',
        'note',
        'display',
        'type',
    ];

    protected $attributes = [
        'type' => 1,
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
