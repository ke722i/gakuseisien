<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 教室の利用不可時間帯（メンテナンス等で予約を止める枠）。
 * (room_id, date, period) 単位で1枠を表す。
 */
class RoomUnavailableSlot extends Model
{
    protected $fillable = [
        'room_id',
        'date',
        'period',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
