<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    protected $casts = [
        'title' => JsonConvertCast::class,
        'description' => JsonConvertCast::class,
        'days' => JsonConvertCast::class,
        'hour' => JsonConvertCast::class
    ];

    public function mentors()
    {
        return $this->belongsToMany(Mentor::class);
    }

    public function syllabuses()
    {
        return $this->hasMany(Syllabus::class);
    }
}
