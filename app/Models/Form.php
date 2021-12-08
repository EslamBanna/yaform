<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $table = 'forms';
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'logo',
        'type',
        'facebook_link',
        'twitter_link',
        'instgram_link'
    ];
}
