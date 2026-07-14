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
}
