<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\JsonConvertCast;

class Project extends Model
{

    protected $fillable = ['title', 'description', 'image'];
    use HasFactory;
    protected  $casts = [
        'title' => JsonConvertCast::class,
        'description' => JsonConvertCast::class
    ];
}
