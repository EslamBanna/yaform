<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormQType extends Model
{
    use HasFactory;

    protected $table = 'form_q_types';
    protected $fillabale = [
        'form_q_id',
        'name'
    ];

    public function questions(){
        return $this->hasMany(FormQuestion::class,'type','form_q_id');
    }
}
