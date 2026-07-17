<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReply extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'author_name',
        'content',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
