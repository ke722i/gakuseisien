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
        'official_url', // 追加済みならここも
        'is_visible',
    ];

    // 1店舗に対して複数の口コミ
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    protected $casts = [
    'payment_method' => 'array',
    'is_visible' => 'boolean',
];
}