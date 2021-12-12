<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultipleAnswer extends Model
{
    use HasFactory;

    protected $table = 'multiple_answers';
    protected $fillable = [
        'answer_id',
        'answer',
    ];

    public function answer()
    {
        return $this->belongsTo(FormAnswer::class, 'answer_id', 'id');
    }
}
