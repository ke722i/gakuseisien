<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 学生向けのお知らせ（通知）。
 * 先生の操作（予約の承認/拒否、届の受理/差し戻し）を学生に伝える。
 */
class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'link_url',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * 通知を1件送る（= テーブルに1行書き込む）ヘルパー。
     * $userId が null のときは何もしない（宛先不明の届などに対応）。
     */
    public static function send(?int $userId, string $title, ?string $body = null, ?string $linkUrl = null): void
    {
        if ($userId === null) {
            return;
        }

        self::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'link_url' => $linkUrl,
        ]);
    }

    /** 未読のみに絞るスコープ */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
