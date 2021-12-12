<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerGroup extends Model
{
    use HasFactory;
    protected $table = 'answer_groups';
    protected $fillable = [
        'user_id',
        'form_id',
        'score'
    ];

    public function answers()
    {
        return $this->hasMany(FormAnswer::class, 'answer_group', 'id');
    }
}
