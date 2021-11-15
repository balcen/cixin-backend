<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'date',
        'name',
        'religion',
        'contact_person',
        'contact_tel',
        'status',
        'note'
    ];
}
