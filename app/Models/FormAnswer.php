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
        'answer',
        'multiple_answer'
    ];

    public function question()
    {
        return $this->belongsTo(FormQuestion::class, 'form_question_id', 'id');
    }

    public function answerGroup()
    {
        return $this->belongsTo(AnswerGroup::class, 'answer_group', 'id');
    }

    public function multipleAnswer()
    {
        return $this->hasMany(MultipleAnswer::class, 'answer_id', 'id');
    }

    // public function getMultipleAnswerAttribute($value){
    //     return ($value->isEmpty() ? 0 : $value);
    // }
    public function getMultipleAnswerAttribute($value){
        return $value;
    }
}
