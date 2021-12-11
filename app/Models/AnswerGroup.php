<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerGroup extends Model
{
    use HasFactory;
    protected $table = '';
    protected $fillable = [
        'user_id',
        'form_id'
    ];

    public function answers()
    {
        return $this->hasMany(FormAnswer::class, 'answer_group', 'id');
    }
}
