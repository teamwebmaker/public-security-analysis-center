<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    public const VISIBILITY_PRIVATE = 'private';
    public const VISIBILITY_PUBLIC = 'public';

    protected $fillable = [
        'title',
        'branch_id',
        'created_by_user_id',
        'document_path',
        'document_original_name',
        'document_mime_type',
        'document_visibility',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function userParticipants()
    {
        return $this->hasMany(IncidentUserParticipant::class);
    }

    public function externalParticipants()
    {
        return $this->hasMany(IncidentExternalParticipant::class);
    }

    public function publicShare()
    {
        return $this->morphOne(PublicShare::class, 'shareable')->latestOfMany();
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match ($user->getRoleName()) {
            User::ADMIN_ROLE => $query,
            'worker' => $query->where('created_by_user_id', $user->id),
            'company_leader', 'responsible_person' => $query->whereHas(
                'userParticipants',
                fn(Builder $participantQuery) => $participantQuery->where('user_id', $user->id)
            ),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public function isPublic(): bool
    {
        return $this->document_visibility === self::VISIBILITY_PUBLIC;
    }
}
