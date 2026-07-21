<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostReply extends Model
{
    use HasFactory;

    // 保存を許可するカラム
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'author_name',
    ];

    /**
     * 紐づく投稿を取得
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * 紐づくユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 親の返信を取得
     */
    public function parent()
    {
        return $this->belongsTo(PostReply::class, 'parent_id');
    }

    /**
     * この返信にぶら下がっている子コメントを取得
     */
    public function children()
    {
        return $this->hasMany(PostReply::class, 'parent_id');
    }

    /**
     * モデルのイベント（削除時の連動処理）
     */
    protected static function booted()
    {
        static::deleting(function ($reply) {
            // 親が削除される時、紐づく子コメントを全て道連れにして削除する
            $reply->children()->delete();
        });
    }
}