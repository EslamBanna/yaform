<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class formQType extends Model
{
    use HasFactory;

    protected $table = 'form_q_types';
    protected $fillabale = [
        'form_q_id',
        'name'
    ];
}
