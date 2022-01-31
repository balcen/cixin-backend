<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'customer_id',
        'date',
        'name',
        'religion',
        'contact_person',
        'contact_tel',
        'status',
        'note',
        'end_date',
    ];
}
