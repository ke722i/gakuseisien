<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'name',
        'genre',
        'address',
        'business_hours',
        'budget',
        'distance',
        'payment_method',
        'is_visible',
    ];
}