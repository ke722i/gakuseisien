<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    const UPDATED_AT = null;
    protected $fillable = [
        'school_id',
        'user_id',
        'title',
        'content',
        'best_answer_id',
        'category'
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
