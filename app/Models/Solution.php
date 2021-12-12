<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    use HasFactory;
    protected $table = 'solutions';
    protected $fillable = [
        'question_id',
        'solution'
    ];

    public function question()
    {
        return $this->belongsTo(FormQuestion::class, 'question_id', 'id');
    }
}
