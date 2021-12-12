<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormQuestion extends Model
{
    use HasFactory;


    protected $table = 'form_questions';
    protected $fillable = [
        'form_id',
        'title',
        'description',
        'type',
        'required'
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }
    public function QuestionType()
    {
        return $this->belongsTo(FormQType::class, 'type', 'form_q_id');
    }
    public function QuestionChoices()
    {
        return $this->hasMany(MultipleChoiceQ::class, 'question_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(FormAnswer::class, 'form_question_id', 'id');
    }

    public function solution()
    {
        return $this->hasMany(Solution::class, 'question_id', 'id');
    }
}
