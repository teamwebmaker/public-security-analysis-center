<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'created_by_user_id',
        'name',
        'surname',
        'position',
        'phone',
        'email',
        'id_number',
        'personal_details_visible',
    ];

    protected $casts = [
        'personal_details_visible' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'employee_branch')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
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
                    'company.users',
                    fn (Builder $users) => $users->where('users.id', $user->id)
                ),
                'responsible_person' => $query->orWhereHas(
                    'branches.users',
                    fn (Builder $users) => $users->where('users.id', $user->id)
                ),
                'worker' => $query->orWhereHas('branches.tasks', function (Builder $tasks) use ($user) {
                    $tasks->active()->whereHas(
                        'users',
                        fn (Builder $workers) => $workers->where('users.id', $user->id)
                    );
                }),
                default => null,
            };
        });
    }
}
