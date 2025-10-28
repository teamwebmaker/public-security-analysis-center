<?php

namespace App\Models;

use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $fillable = [
        "name",
        "document",
        "visibility",
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_document_templates')->withTimestamps();
    }

}