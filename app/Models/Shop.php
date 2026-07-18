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

    /** この店舗をお気に入り登録しているユーザー */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'shop_favorites')->withTimestamps();
    }

    /** 指定ユーザーがこの店舗をお気に入り登録しているか */
    public function isFavoritedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        // リレーションを読み込み済みならクエリを投げない
        if ($this->relationLoaded('favoritedBy')) {
            return $this->favoritedBy->contains('id', $user->id);
        }

        return $this->favoritedBy()->where('users.id', $user->id)->exists();
    }
}