<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
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
        return $this->hasMany(OrderUserParticipant::class);
    }

    public function publicShare()
    {
        return $this->morphOne(PublicShare::class, 'shareable')->latestOfMany();
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user) {
            $query->where('created_by_user_id', $user->id)
                ->orWhereHas(
                    'userParticipants',
                    fn(Builder $participantQuery) => $participantQuery->where('user_id', $user->id)
                );
        });
    }

    public function isPublic(): bool
    {
        return $this->document_visibility === self::VISIBILITY_PUBLIC;
    }
}
