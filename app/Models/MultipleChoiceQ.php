<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultipleChoiceQ extends Model
{
    use HasFactory;

    protected $table = 'multiple_choice_q_s';
    protected $fillable = [
        'question_id',
        'choice'
    ];

    public function question()
    {
        return $this->belongsTo(FormQuestion::class, 'question_id', 'id');
    }
}
