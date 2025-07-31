<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'role_id',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * List of management roles
     * @var array
     */
    public const MANAGEMENT_ROLES = [
        'company_leader',
        'responsible_person',
        'worker',
    ];
    public const ADMIN_ROLE = 'admin';


    /**
     * Determine user is an admin or not
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role->name === self::ADMIN_ROLE;
    }

    /**
     * Determine if the user role is in management roles
     * @return bool
     */
    public function isManagementUser(): bool
    {
        return in_array($this->role->name, self::MANAGEMENT_ROLES);
    }

    /**
     * Get the user's role name (e.g., "admin").
     */
    public function getRoleName(): string
    {
        return $this->role->name ?? 'unknown';
    }

    public function scopeWithoutAdmins($query)
    {
        // Assuming role_id 1 = admin
        return $query->whereHas(
            'role',
            fn($q) =>
            $q->where('name', '!=', self::ADMIN_ROLE)
        );
    }

    // Connections 
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_leaders')->withTimestamps();
    }
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'responsible_person_branch')->withTimestamps();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'responsible_person_service')->withTimestamps();
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_workers')->withTimestamps();
    }
}
