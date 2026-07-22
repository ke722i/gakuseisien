<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'user_id', 'reason', 'post_id', 'answer_id', 'type'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /** 通報対象の回答（Q&Aの回答への通報のみ） */
    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /** 通報したユーザー */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
