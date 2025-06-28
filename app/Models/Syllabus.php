<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $table = 'syllabuses';

    protected $fillable = ['title', 'pdf', 'program_id', 'visibility'];

    protected $casts = [
        'title' => JsonConvertCast::class,
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
