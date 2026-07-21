<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'shop_id',
        'rating',
        'comment',
        'is_visible',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}