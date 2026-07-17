<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['login_id', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * 先生アカウントかどうかを判定する。
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * upvoteのカウント
     */
    public function upvotedAnswers()
    {
        return $this->belongsToMany(Answer::class, 'answer_user')->withTimestamps();
    }

    public function questions()
    {
        return $this->hasMany(\App\Models\Question::class, 'user_id');
    }
}
