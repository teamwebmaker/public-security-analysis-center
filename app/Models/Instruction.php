<?php

namespace App\Models;

use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    public const VISIBILITY_PRIVATE = 'private';

    public const VISIBILITY_PUBLIC = 'public';

    protected $fillable = [
        'name',
        'link',
        'document',
        'document_original_name',
        'document_mime_type',
        'document_visibility',
        'visibility',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'instructions_workers')->withTimestamps();
    }

    public function publicShare()
    {
        return $this->morphOne(PublicShare::class, 'shareable')->latestOfMany();
    }

    public function publicShares()
    {
        return $this->morphMany(PublicShare::class, 'shareable');
    }

    public function isPublic(): bool
    {
        return $this->document_visibility === self::VISIBILITY_PUBLIC;
    }
}
