<?php

namespace App\Models;
use App\Casts\JsonConvertCast;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    use HasVisibilityScope;
    protected $fillable = ['title', 'description', 'image', 'visibility'];
    protected $casts = [
        'title' => JsonConvertCast::class,
        'description' => JsonConvertCast::class
    ];
}
