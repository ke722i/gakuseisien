<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category',
        'location',
        'image_url',
        'posted_by',
        'user_id',
        'reply_count',
        'last_replied_at',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'last_replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(PostReply::class)->whereNull('parent_id')->orderBy('created_at', 'asc');
    }
}
