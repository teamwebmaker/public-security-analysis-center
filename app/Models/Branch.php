<?php

namespace App\Models;

use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $fillable = [
        'name',
        'address',
        'company_id',
        'visibility',
    ];

    // If the name of the branch is updated, update the branch name field in tasks
    protected static function booted()
    {
        static::updated(function ($branch) {
            $changes = $branch->getChanges();
            if (array_key_exists('name', $changes)) {
                $branch->tasks()->update(['branch_name_snapshot' => $branch->name]);
            }
        });

    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'responsible_person_branch')->withTimestamps();
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
