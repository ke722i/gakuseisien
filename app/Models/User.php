<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'login_id',
    'password',
    'role',
    'student_number',
    'class_number',
    'student_name',
    'homeroom_teacher',
    'teacher_number',
    'teacher_name',
])]
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

    /** お気に入り登録した店舗 */
    public function favoriteShops()
    {
        return $this->belongsToMany(Shop::class, 'shop_favorites')->withTimestamps();
    }
}
