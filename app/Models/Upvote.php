<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upvote extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'user_id', 
        'answer_id
    '];
}
