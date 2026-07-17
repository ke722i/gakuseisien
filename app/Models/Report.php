<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'user_id', 'reason', 'post_id', 'type'];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
