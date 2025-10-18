<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\HasVisibilityScope;

class Instruction extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $fillable = [
        "name",
        "link",
        "document",
        "visibility",
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'instructions_workers')->withTimestamps();
    }

}
