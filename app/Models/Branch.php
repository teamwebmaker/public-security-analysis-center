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
