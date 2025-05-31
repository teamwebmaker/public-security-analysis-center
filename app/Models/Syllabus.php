<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    protected $table= 'syllabuses';
    protected  $casts = [
        'title' => JsonConvertCast::class,
    ];
    use HasFactory;
}
