<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormAnswer extends Model
{
    use HasFactory;

    protected $table = 'form_answers';
    protected $fillable = [
        'answer_group',
        'form_question_id',
        'answer'
    ];

    public function question()
    {
        return $this->belongsTo(FormQuestion::class, 'form_question_id', 'id');
    }

    public function answerGroup()
    {
        return $this->belongsTo(AnswerGroup::class, 'answer_group', 'id');
    }
}
