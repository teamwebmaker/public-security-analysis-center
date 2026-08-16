<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MaterialEquipment extends Model
{
    public const VISIBILITY_PRIVATE = 'private';

    public const VISIBILITY_PUBLIC = 'public';

    protected $table = 'material_equipments';

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

    public function signatures()
    {
        return $this->hasMany(MaterialEquipmentSignature::class);
    }

    public function publicShare()
    {
        return $this->morphOne(PublicShare::class, 'shareable')->latestOfMany();
    }

    public function publicShares()
    {
        return $this->morphMany(PublicShare::class, 'shareable');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user) {
            $query->where('created_by_user_id', $user->id);

            match ($user->getRoleName()) {
                'company_leader' => $query->orWhereHas(
                    'branch.company.users',
                    fn (Builder $users) => $users->where('users.id', $user->id)
                ),
                'responsible_person' => $query->orWhereHas(
                    'branch.users',
                    fn (Builder $users) => $users->where('users.id', $user->id)
                ),
                'worker' => $query->orWhereHas('branch.tasks', function (Builder $tasks) use ($user) {
                    $tasks->active()->whereHas(
                        'users',
                        fn (Builder $workers) => $workers->where('users.id', $user->id)
                    );
                }),
                default => null,
            };
        });
    }

    public function isPublic(): bool
    {
        return $this->document_visibility === self::VISIBILITY_PUBLIC;
    }
}
