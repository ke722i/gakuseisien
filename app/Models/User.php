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
    'must_change_password',
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
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * 欠席届などで自動入力に使う表示名。
     * 学生は氏名、教職員は教員名。未設定ならログインIDで代用する。
     */
    public function displayName(): string
    {
        return $this->isTeacher()
            ? ($this->teacher_name ?: $this->login_id)
            : ($this->student_name ?: $this->login_id);
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
