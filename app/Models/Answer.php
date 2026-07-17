<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'question_id',
        'user_id',
        'content',
        'is_teacher_approved',
        'upvote_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function upvoters()
    {
        return $this->belongsToMany(User::class, 'answer_user')->withTimestamps();
    }

    public function question()
    {
        // 回答は一つの質問に属している
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * 指定したユーザーがこの回答にすでに投票しているかをチェック
     */
    public function isUpvotedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->upvoters()->where('user_id', $user->id)->exists();
    }

    // app/Models/Answer.php 内に追記
    public function replies()
    {
        return $this->hasMany(Answer::class, 'parent_id')->orderBy('created_at', 'asc');
    }
}
