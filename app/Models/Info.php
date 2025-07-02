<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    protected $fillable = ['title', 'description', 'experience', 'graduates', 'image', 'phone', 'email',];
    protected $casts = [
        'title' => JsonConvertCast::class,
        'description' => JsonConvertCast::class,
    ];
    use HasFactory;
}
