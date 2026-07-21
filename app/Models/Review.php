<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'rating',
        'comment',
        'edit_token',
        'is_visible',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}