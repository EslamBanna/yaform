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
}
