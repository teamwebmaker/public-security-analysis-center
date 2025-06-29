<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $fillable = [
        'full_name',
        'image',
        'description',
        'visibility',
    ];

    protected $casts = [
        'description' => JsonConvertCast::class
    ];

    public function programs()
    {
        return $this->belongsToMany(Program::class);
    }
}
