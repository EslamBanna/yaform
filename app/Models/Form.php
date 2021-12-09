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

    public function getLogoAttribute($value)
    {
        $actual_link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
        return ($value == null ? '' : $actual_link . 'images/forms_logo/' . $value);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function formQuestions()
    {
        return $this->hasMany(FormQuestion::class, 'form_id', 'id');
    }
}
