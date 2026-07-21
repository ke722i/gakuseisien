<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'floor',
        'room_code',
        'name',
        'room_type',
        'is_reservable',
        'display_order',
        'pos_x',
        'pos_y',
        'width',
        'height',
    ];

    protected $casts = [
        'is_reservable' => 'boolean',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
