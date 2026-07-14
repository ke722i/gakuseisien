<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'category',
        'start_at',
        'end_at',
        'all_day',
        'location',
        'description',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'all_day' => 'boolean',
    ];

    /**
     * カテゴリ名 → CSSクラス（色）の対応。
     */
    public const CATEGORY_CLASSES = [
        'イベント' => 'event',
        '行事' => 'gyoji',
        '休校' => 'kyuko',
        '試験日' => 'exam',
        'その他' => 'other',
    ];

    /**
     * このイベントのカテゴリに対応するCSSクラスを返す。
     */
    public function categoryClass(): string
    {
        return self::CATEGORY_CLASSES[$this->category] ?? 'other';
    }
}
