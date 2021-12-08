<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormAnswer extends Model
{
    use HasFactory;

    protected $table = 'form_answers';
    protected $fillable = [
        'form_question_id',
        'answer'
    ];
}
