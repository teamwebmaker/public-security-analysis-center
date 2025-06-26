<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;
    use HasVisibilityScope;
    protected $fillable = ['title', 'description', 'image', 'file', 'visibility'];
    protected $casts = [
        'title' => JsonConvertCast::class,
        'description' => JsonConvertCast::class
    ];
}
